<?php

namespace App\Http\Controllers;

use App\Task;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\cache;
use Illuminate\Support\Facades\Redis;

class TaskController extends Controller
{
    /**
     * Task controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Show all tasks and set cache
     */
    public function index() {
        // Use of Redis cache
        $tasks = Cache::remember('task', 10, function() {
            return Task::all();
        });
        return $tasks->toJson();
    }

    /**
     * Show tasks by filters
     * @param request [due_date, completed, date_creation, date_update]
     */
    public function list(Request $request) {

        if($request->has('id')) {

            // Ger input ID
            $id = $request->input('id');

            // Use of Redis cache
            $task = Cache::remember('task_id_' . $request->input('id'), 10, function() use ($id) {
                return Task::find($id);
            });

            if($task) {
                return $task->toJson();
            } else {
                return response()->json("Error: No record found for that id", 400);
            }
        } else {
            // Prepare query
            $data = Task::query();

            // Set key to Cache
            $key = 'task';

            // If exists, add due_date filter
            if($request->has('due_date')) {
                // Valid that it is a valid date
                $date = $request->input('due_date');

                $dt = \DateTime::createFromFormat("Y-m-d", $date);

                // Valid that the date format is Y-m-d
                if($dt !== false && !array_sum($dt->getLastErrors())) {
                    $data = $data->where('due_date', $date);
                } else {
                    // There was an error in date format
                    return response()->json("Validity error: Field due_date must be in format Y-m-d", 400);
                }
                $key .= '_' . $date;
            }

            // If exists, add completed filter
            if($request->has('completed')) {
                // Format field completed to "true" or "false"
                $completed = $request->input('completed');
                if($completed) {
                    $complete = "true";
                } else {
                    $complete = "false";
                }
                $data = $data->where('completed', $complete);

                $key .=  '_' . $completed;
            }

            // If exists, add created_at filter
            if($request->has('date_creation')) {
                // Valid that it is a valid date
                $dateCreation = $request->input('date_creation');

                $dtCreation = \DateTime::createFromFormat("Y-m-d", $dateCreation);

                // Valid that the date format is Y-m-d
                if($dtCreation !== false && !array_sum($dtCreation->getLastErrors())) {
                    $data = $data->where('created_at', $request->input('date_creation'));
                } else {
                    // There was an error in date format
                    return response()->json("Validity error: Field date_creation must be in format Y-m-d", 400);
                }

                $key .= '_' . $dateCreation;
            }

            // If exists, add updated_at filter
            if($request->has('date_update')) {
                // Valid that it is a valid date
                $dateUpdate = $request->input('date_update');

                $dtUpdate = \DateTime::createFromFormat("Y-m-d", $dateUpdate);

                // Valid that the date format is Y-m-d
                if($dtUpdate !== false && !array_sum($dtUpdate->getLastErrors())) {
                    $data = $data->where('updated_at', $request->input('date_update'));
                } else {
                    // There was an error in date format
                    return response()->json("Validity error: Field date_update must be in format Y-m-d", 400);
                }

                $key .= '_' . $dateUpdate;
            }

            if($request->has('next')) {
                $data->where('_id', '>=', $request->input('next'));
                $key .= '_' . $request->input('next');
            }

            // Use of Redis cache
            $results = Cache::remember($key, 10, function() use ($data) {
                // get all data
                return $data->get()->take(6)->sortBy('_id');
            });
            
            $dataResult = array();
            // Format data result
            for($i = 0; $i < count($results); $i++) {
                $dataResult['data'][] = [
                    '_id'           => $results[$i]->_id,
                    'title'         => $results[$i]->title,
                    'description'   => $results[$i]->description,
                    'due_date'      => $results[$i]->due_date,
                    'date_creation' => $results[$i]->created_at,
                    'date_update'   => $results[$i]->updated_at,
                ];
            }

            // If it exists, I include the next field
            if(count($results) == 6) {
                $dataResult['next'] = $results[5]->_id;
            }

            return json_encode($dataResult);
        }
    }

    public function store(Request $request) {

        $date = $request->input('due_date');

        $dt = \DateTime::createFromFormat("Y-m-d", $date);

        // Validation of required fields
        // Field due_date is required
        if(!$request->has("due_date")) {
            return response()->json("Validity error: Field due_date is required", 400);
        }
        
        // Field title is required
        if(!$request->has("title")) {
            return response()->json("Validity error: Field title is required", 400);
        }

        // By default, completed field is false
        if($request->has('completed')) {
            $completed = $request->input('completed');
            if($completed) {
                $complete = "true";
            } else {
                $complete = "false";
            }
        } else {
            $complete = "false";
        }
        
        // Valid that the date format is Y-m-d
        if($dt !== false && !array_sum($dt->getLastErrors())) {
            // If there is no error in the date format, the record is stored
            $data = Task::create([
                "title"         => $request->input('title'),
                "description"   => $request->input('description'),
                "due_date"      => $request->input('due_date'),
                "completed"     => $complete,
                "created_at"    => Carbon::now()->format('Y-m-d'),
                "updated_at"    => "",
            ]);

            // Cleaning the cache after inserting a record
            Cache::flush();

            return response()->json("The record has been stored", 200);
        } else {
            // There was an error in date format
            return response()->json("Validity error: Field due_date must be in format Y-m-d", 400);
        }
    }

    public function edit(Request $request) {
        // Validation of required fields
        if(!$request->has("id")) {
            return response()->json("Validity error: Field id is required", 400);
        }

        // Find data
        $data = Task::find($request->input('id'));

        if($data) {
            // If it exists, only the data sent will be updated

            if($request->has('due_date')) {
                $date = $request->input('due_date');

                $dt = \DateTime::createFromFormat("Y-m-d", $date);

                // Valid that the date format is Y-m-d
                if($dt !== false && !array_sum($dt->getLastErrors())) {
                    // If there is no error in the date format, the record will be updated
                    $data->due_date = $request->input('due_date');
                } else {
                    // There was an error in date format
                    return response()->json("Validity error: Field due_date must be in format Y-m-d", 400);
                }
            }

            // By default, completed field is false
            if($request->has('completed')) {
                $complete = "false";
                $completed = $request->input('completed');
                if($completed) {
                    $complete = "true";
                }

                // Update field completed
                $data->completed = $complete;
            }

            if($request->has('title')) {
                // Update field title
                $data->title = $request->input('title');
            }

            if($request->has('description')) {
                // Update field description
                $data->description = $request->input('description');
            }

            $data->updated_at = Carbon::now()->format('Y-m-d');

            $data->update();

            // Cleaning the cache after updating a record
            Cache::flush();

            return response()->json("The record has been updated", 200);
        } else {
            // If not exists
            return response()->json("Error: No record found for that id", 400);
        }
    }

    public function destroy(Request $request) {
        // Validation of required fields
        if(!$request->has("id")) {
            return response()->json("Validity error: Field id is required", 400);
        }

        // Find data
        $data = Task::find($request->input('id'));

        if($data) {
            // If exists
            $data->delete();

            // Cleaning the cache after deleting a record
            Cache::flush();

            return response()->json("The record has been deleted", 200);
        } else {
            // If not exists
            return response()->json("Error: No record found for that id", 400);
        }
    }
}

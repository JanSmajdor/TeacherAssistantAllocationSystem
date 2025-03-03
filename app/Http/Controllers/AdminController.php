<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\AreaOfKnowledge;
use App\Models\ModuleAreasOfKnowledge;
use App\Models\Module;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function showAreaOfKnowledgeForm()
    {
        return view('new_area_of_knowledge');
    }

    public function createAreaOfKnowledge(Request $request)
    {
        //still need to add proper request data verification

        //check if this AoK exists already
        try{
            $new_area_of_knowledge = AreaOfKnowledge::firstOrCreate([
                'name' => $request->input('aok-name')
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error Adding Area of Knowledge to the Database:' . $e->getMessage());
        }
        
        if ($new_area_of_knowledge) {
            return redirect()->back()->with('error', 'Error Adding Area of Knowledge to the Database: Already Exists');
        }

        return redirect()->back()->with('success', 'Area of Knowledge has been Succesfully Added to the Database!');
    }

    public function showModuleForm()
    {
        //still need to add proper request data verification

        // all module leaders, used for ML dropdown field. all aok used for aok dropdown field
        $module_leaders = User::select('id', 'first_name', 'last_name')->where('role', 'Module Leader')->get(); //potentially modify this to stop showing ML if they are already assigned to a module
        $aok = AreaOfKnowledge::select('id', 'name')->get();

        return view('new_module')->with('module_leaders', $module_leaders)->with('aok', $aok);
    }

    public function createModule(Request $request)
    {   
        
        try{
            DB::beginTransaction();

            $new_module = Module::firstOrCreate([
                'module_leader_id' => $request->input('module-leader'),
                'module_name' => $request->input('module-name'),
                'module_code' => $request->input('module-code'),
                'num_of_students' => $request->input('number-of-students')
            ]);

            $module_aok = ModuleAreasOfKnowledge::firstOrCreate([
                'area_id' => $request->input('module-area-of-knowledge'),
                'module_id' => $new_module->id
            ]);
            
            DB::commit();
            return redirect()->back()->with('success', 'Module has been Succesfully Added to the Database!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error Adding Module to the Database:' . $e->getMessage());
        }
    }
}

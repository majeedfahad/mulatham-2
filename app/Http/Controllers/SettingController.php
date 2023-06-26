<?php

namespace App\Http\Controllers;

use App\Models\Elimination;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        return view('admin.index');
    }

    public function admin()
    {
        $settings = Setting::all();

        return view('admin.settings', compact('settings'));
    }

    public function users()
    {
        $users = User::query()->competitors()->orderBy('status', 'desc')->get();

        return view('admin.users', compact('users'));
    }

    public function activeSetting($id)
    {
        Setting::find($id)->update(['value' => 1]);

        return back();
    }
    public function deActiveSetting($id)
    {
        Setting::find($id)->update(['value' => 0]);

        return back();
    }

    public function elimination(Request $request)
    {
        $attacker = User::find($request['attacker']);
        $target = User::where('fakename',$request['fakename'])->first();
        $targetName = $request['target'];
        $result = Elimination::fight($attacker, $target, $targetName);

        if($result)
            return back()->with(['success' => 'صح عليك! مبروك عليك نقاطه']);
        return back()->with(['failed' => 'لاااي لاي لاي لاااي، فمان الله ونقاطك راحت له']);
    }
}

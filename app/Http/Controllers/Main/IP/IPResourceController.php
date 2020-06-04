<?php

namespace App\Http\Controllers\Main\IP;

use App\Core\Constants\ListAccountTypeConstants;
use App\Core\systems\main\ips\IPExcelFileReader;
use App\Http\Controllers\Controller;
use App\Models\Main\IP\IPModel;
use App\Models\Main\Staff\EmployeeModel;
use App\Models\Main\Storage\EmployeeFileModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Exception;


class IPResourceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->authorizeResource(IPModel::class, 'ip');
    }

    static public function assignFile($file) {
        try {
            $ipFile = new IPExcelFileReader(
                str_replace('/', '\\', storage_path() . '/app/' . $file->path)
            );

            $ipFile = $ipFile->getResult();
        } catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
            Session::flash('errorMessage', 'Произошла ошибка при добавлении');
            return back();
        } catch (Exception $e) {
            Session::flash('errorMessage', 'Произошла ошибка при добавлении');
            return back();
        }

        $teacher = EmployeeModel::all()
            ->where('secondName', '=', $ipFile[0]['secondName'])
            ->where('firstName', '=', $ipFile[0]['firstName'])
            ->where('patronymic', '=', $ipFile[0]['patronymic'])
            ->first();

        if ($teacher) {
            $ip = new IPModel();
            $ip->idEmployeeFile = $file->idEmployeeFile;
            $ip->idTeacher = $teacher->idEmployee;
            $ip->educationYear = $ipFile[0]['educationYear'];
            $ip->lastEmployee = Auth::user()->getEmployee()->idEmployee;
            $ip->lastUpdate = date('Y-m-d H:i:s');

            return $ip->save();
        }

        return false;
    }

    public function downloadIP($ip)
    {
        $ipFile = IPModel::query()->where('idIP', '=', $ip)->first();
        $file = EmployeeFileModel::query()->where('idEmployeeFile', '=', $ipFile->idEmployeeFile)->first();

        if (Storage::exists($file->path)) {
            return Storage::download($file->path, $file->filename);
        }

        Session::flash('message', 'Не удалось скачать ИП');
        return back();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        switch (Auth::user()->getIdAccountType())
        {
            case ListAccountTypeConstants::TEACHER:
            {
                $ips = IPModel::all()->where('idTeacher', '=', Auth::user()->getEmployee()->getTeacher()->idTeacher);

                return view('systems.main.ips.ip_index', [
                    'ips' => $ips
                ]);
            } break;
            case ListAccountTypeConstants::METHODIST:
            {
                $ips = IPModel::all();

                return view('systems.main.ips.ip_index', [
                    'ips' => $ips
                ]);
            } break;
        }

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('systems.main.ips.ip_add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $data = $request->only(['files', 'lastEmployee', 'lastUpdate']);


        $result = true;
        foreach ($data['files'] as $file) {
            $ipFile = new IPExcelFileReader(
                str_replace('/', '\\' , storage_path() . '/app/' . EmployeeFileModel::all()->where('idEmployeeFile', '=', $file)->first()->path)
                );

            try
            {
                $ipFile = $ipFile->getResult();
            } catch (Exception $e) {
                Session::flash('errorMessage', 'Произошла ошибка при добавлении');
                return back();
            }


            $idTeacher = DB::table('employees as e')
                ->select('t.idTeacher')
                ->join('Teachers as t', 't.idEmployee', '=','e.idEmployee')
                ->where('e.secondName', '=', $ipFile[0]['secondName'])
                ->where('e.firstName', '=', $ipFile[0]['firstName'])
                ->where('e.patronymic', '=', $ipFile[0]['patronymic'])
                ->get()->first()->idTeacher;

            $ip = new IPModel();
            $ip->idEmployeeFile = $file;
            $ip->idTeacher = $idTeacher;
            $ip->educationYear = $ipFile[0]['educationYear'];
            $ip->lastEmployee = Auth::user()->getEmployee()->idEmployee;
            $ip->lastUpdate = date('Y-m-d H:i:s');
            $result *= $ip->save();
        }

        if ($result) {
            Session::flash('successMessage', 'ИП успешно добавлен');
            return back();
        }

        Session::flash('errorMessage', 'Произошла ошибка при добавлении');
        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Main\IP\IPModel  $iP
     * @return \Illuminate\Http\Response
     */
    public function show(IPModel $iP)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Main\IP\IPModel  $iP
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(IPModel $ip)
    {
        $ipExcelFileStreamer = new IPExcelFileReader($ip->getFullFilePath());

        $ipFile = $ipExcelFileStreamer->getResult();

        #dd($ipFile);

        $idTeacher = DB::table('employees as e')
            ->select('t.idTeacher')
            ->join('Teachers as t', 't.idEmployee', '=','e.idEmployee')
            ->where('e.secondName', '=', $ipFile[0]['secondName'])
            ->where('e.firstName', '=', $ipFile[0]['firstName'])
            ->where('e.patronymic', '=', $ipFile[0]['patronymic'])
            ->get()->first()->idTeacher;

        if (!empty($ip)) {
            return view('systems.main.ips.ip_update', [
                'ip' => $ip,
                'idTeacher' => $idTeacher,
                'file' => $ipFile
            ]);
        }

        Session::flash('message', 'Произошла ошибка');
        return Redirect::route('ips.index');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Main\IP\IPModel  $iP
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, IPModel $ip)
    {
        date_default_timezone_set('Europe/Moscow');
        $ip->lastEmployee = Auth::user()->idEmployee;
        $ip->lastUpdate = date('Y-m-d H:i:s');
        if ($ip->update()) {
            Session::flash('message', 'ИП обновлён');
            return Redirect::route('ips.edit', $ip);
        }

        Session::flash('message', 'Не удалось сохранить ИП');
        return Redirect::route('ips.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Main\IP\IPModel  $iP
     * @return \Illuminate\Http\Response
     */
    public function destroy(IPModel $ip)
    {
        if ($ip->delete()) {
            Session::flash('successMessage', 'ИП больше не отслеживается');
            return back();
        }

        Session::flash('errorMessage', 'Ошибка удаления');
        return Redirect::route('ips.index');
    }
}
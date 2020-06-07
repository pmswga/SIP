<?php

namespace App\Http\Controllers\Main\Storage;

use App\Core\Constants\ListFileTagConstants;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Main\IP\IPResourceController;
use App\Models\Main\IP\IPModel;
use App\Models\Main\Storage\EmployeeFileModel;
use App\Models\Main\Storage\ListFileTagModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;

class EmployeeFileResourceController extends Controller
{
    private $baseAccountPath;

    public function __construct()
    {
        $this->authorizeResource(EmployeeFileModel::class, 'file');
        $this->baseAccountPath = 'storage/';
    }

    private function getAccountPath() {
        return $this->baseAccountPath . Auth::user()->getEmail();
    }

    public function downloadFile(EmployeeFileModel $file) {
        if (Storage::exists($file->getPath())) {
            return Storage::download($file->getPath());
        }

        Session::flash('message', ['type' => 'error', 'message' => 'Не удалось скачать файл']);
        return back();
    }

    public function createDirectory(Request $request) {
        $data = $request->only(['currentDirectory', 'directoryName']);

        if (Storage::exists($data['currentDirectory'])) {
            $path = $data['currentDirectory']. DIRECTORY_SEPARATOR . str_replace(' ', '_', $data['directoryName']);

            if (!Storage::exists($path)) {
                if (Storage::makeDirectory($path)) {
                    Session::flash('message', ['type' => 'success', 'message' => 'Папка создана']);
                    return back();
                }
            }

            Session::flash('message', ['type' => 'warning', 'message' => 'Папка уже существует']);
            return back();
        }

        Session::flash('message', ['type' => 'error', 'message' => 'Не удалось создать папку']);
        return back();
    }

    public function destroyDirectory(Request $request) {
        $data = $request->only(['currentDirectory', 'directoryName']);

        try
        {
            if (Storage::exists($data['currentDirectory'])) {
                $path = $data['currentDirectory']. DIRECTORY_SEPARATOR . $data['directoryName'];

                if (EmployeeFileModel::all()->where('directory', '=', $path)->count() == 0) {
                    if (Storage::exists($path)) {
                        if (Storage::deleteDirectory($path)) {
                            Session::flash('message', ['type' => 'success', 'message' => 'Папка удалена']);
                            return back();
                        }
                    }

                    Session::flash('message', ['type' => 'error', 'message' => 'Невозможно удалить папку, так как в ней содержатся файлы']);
                    return back();
                }

                Session::flash('message', ['type' => 'warning', 'message' => 'Невозможно удалить папку, так как в ней содержатся файлы']);
                return back();
            }

            Session::flash('message', ['type' => 'error', 'message' => 'Не удалось удалить папку']);
            return back();
        } catch (\ErrorException $errorException) {
            Session::flash('message', ['type' => 'warning', 'message' => 'Папка содержит подпапки']);
            return back();
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        // #fixme
        if (!Storage::exists($this->getAccountPath())) {
            Storage::makeDirectory($this->getAccountPath());
        }

        $path = '';
        $parentDirectory = '';
        if ($request->path) {
            $path = $request->path;
            if ($request->path != $this->getAccountPath()) {
                $parentDirectory = dirname($path);
            }
        } else {
            $path = $this->getAccountPath();
        }

        $allDirectories = Storage::directories($path);

        $files = EmployeeFileModel::all()->where('directory', '=', $path);


        return view('systems.main.storage.files_index', [
            'currentDirectory' => $path,
            'parentDirectory'  => $parentDirectory,
            'fileTags' => ListFileTagModel::all(),
            'folders' => $allDirectories,
            'files' => $files
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $file = $request->file;
        $currentDirectory = $request->currentDirectory;

        if (Storage::exists($currentDirectory)) {
            $path = Storage::putFileAs($currentDirectory, $file, $file->getClientOriginalName());
            $pathInfo = pathInfo($path);

            if (Storage::exists($path)) {
                $isExist = EmployeeFileModel::query()
                    ->where('filename', '=', $pathInfo['filename'])
                    ->where('idEmployee', '=', Auth::id())
                    ->get();

                if ($isExist->isEmpty()) {
                    $fileModel = new EmployeeFileModel([
                        'idEmployee' => Auth::user()->idEmployee,
                        'idFileTag' => $request->fileTag,
                        'directory' => $currentDirectory,
                        'path' => $pathInfo['dirname'] . '/' . $pathInfo['basename'],
                        'filename' => $pathInfo['filename'],
                        'extension' => $pathInfo['extension']
                    ]);

                    if ($fileModel->save()) {
                        switch ($request->fileTag)
                        {
                            case ListFileTagConstants::IP:
                            {
                                IPResourceController::assignFile($fileModel);
                            } break;
                        }

                        Session::flash('message', ['type' => 'success', 'message' => 'Файл загружен']);
                        return back();
                    } else {
                        Storage::delete($path);
                    }
                } else {
                    Session::flash('message', ['type' => 'warning', 'message' => 'Такой файл уже был добавлен ранее']);
                    return back();
                }
            }
        }

        Session::flash('message', ['type' => 'error', 'message' => 'Произошла ошибка загрузки файла']);
        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Main\Storage\EmployeeFileModel  $employeeFileModel
     * @return \Illuminate\Http\Response
     */
    public function show(EmployeeFileModel $employeeFileModel)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Main\Storage\EmployeeFileModel  $employeeFileModel
     * @return \Illuminate\Http\Response
     */
    public function edit(EmployeeFileModel $employeeFileModel)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Main\Storage\EmployeeFileModel  $employeeFileModel
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, EmployeeFileModel $employeeFileModel)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Main\Storage\EmployeeFileModel  $employeeFileModel
     * @return \Illuminate\Http\Response
     */
    public function destroy(EmployeeFileModel $file)
    {
        if (Storage::exists($file->path)) {
            switch ($file->idFileTag)
            {
                case ListFileTagConstants::IP:
                {
                    $ipFile = IPModel::all()->where('idEmployeeFile', '=', $file->idEmployeeFile)->first();
                    if ($ipFile) {

                        $ipFile->delete();
                    }
                } break;
            }

            if (Storage::delete($file->path)) {
                if ($file->delete()) {
                    Session::flash('message', ['type' => 'success', 'message' => 'Файл удалён']);
                    return back();
                }
            }
        }

        Session::flash('message', ['type' => 'error', 'message' => 'Произошла ошибка при удалении файла']);
        return back();
    }
}

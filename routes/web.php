<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

use App\Activity;
use App\User;
use App\UserActivity;
use Carbon\Carbon;
use Illuminate\Http\Request;

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->get('/kegiatan', function () {
    return response()->json(['data' => Activity::all()]);
});

$router->post('/kegiatan', function (Request $request) {
    $name_img = $request->tanggal . 'T' . time() . '.' . $request->img->extension();
    $request->img->move('upload', $name_img);
    $dataActivity = array(
        'tanggal' => $request->tanggal,
        'mulai' => $request->mulai,
        'akhir' => $request->akhir,
        'deskripsi' => $request->deskripsi,
        'img' => $name_img,
        'user' => $request->user
    );
    $activity_id = Activity::create($dataActivity)->id;
    $user = explode(',', $request->user);
    foreach ($user as $el) {
        UserActivity::create(['activity_id' => $activity_id, 'user_id' => $el]);
    }

    return response()->json(['data' => $dataActivity]);
});

$router->post('/foto', function (Request $request) {
    $name_img = $request->tanggal . 'T' . time() . '.' . $request->img->extension();
    $request->img->move('upload', $name_img);
    $activity = Activity::find($request->id);
    $activity->img = $name_img;
    $activity->save();
    return response()->json(['data' => $activity]);
});

$router->get('/genword', function () {
    $word = new \PhpOffice\PhpWord\PhpWord();
    $section = $word->addSection(['orientation' => 'landscape']);
    foreach (Activity::select('tanggal')->groupBy('tanggal')->get()->toArray() as $item) {
        // judul
        $section->addText('LAPORAN KEGIATAN HARIAN', ['name' => 'Times New Roman', 'size' => 12, 'bold' => true], ['alignment' => 'center']);
        $section->addText('KKN TEMATIK UNIVERSITAS PENDIDIKAN INDONESIA 2019', ['name' => 'Times New Roman', 'size' => 12, 'bold' => true], ['alignment' => 'center']);
        $section->addTextBreak();
        // informasi
        $section->addText(htmlspecialchars("Tema\t\t\t: Citarum Harum"), ['name' => 'Times New Roman', 'size' => 12], ['alignment' => 'left']);
        $section->addText(htmlspecialchars("Kelompok\t\t: 1"), ['name' => 'Times New Roman', 'size' => 12], ['alignment' => 'left']);
        $section->addText(htmlspecialchars("Desa/Kelurahan\t: Sukamenak"), ['name' => 'Times New Roman', 'size' => 12], ['alignment' => 'left']);
        $section->addText(htmlspecialchars("Kecamatan\t\t: Margahayu"), ['name' => 'Times New Roman', 'size' => 12], ['alignment' => 'left']);
        $section->addText(htmlspecialchars("Kabupaten\t\t: Bandung"), ['name' => 'Times New Roman', 'size' => 12], ['alignment' => 'left']);
        $section->addText(htmlspecialchars("Provinsi\t\t: Jawa Barat"), ['name' => 'Times New Roman', 'size' => 12], ['alignment' => 'left']);
        $section->addText(htmlspecialchars("Hari/Tanggal\t\t: " . Carbon::parse($item['tanggal'])->locale('id')->format('l, d F Y')), ['name' => 'Times New Roman', 'size' => 12], ['alignment' => 'left']);
        $section->addTextBreak();
        // table
        $table = $section->addTable(['borderSize' => 5, 'layout' => 'autofit', 'width' => 100, 'cellMargin' => 100]);
        $table->addRow(900);
        $table->addCell(400, ['valign' => 'center'])->addText("No.", ['name' => 'Times New Roman', 'size' => 12, 'bold' => true], ['align' => 'center']);
        $table->addCell(1000, ['valign' => 'center'])->addText("Waktu", ['name' => 'Times New Roman', 'size' => 12, 'bold' => true], ['align' => 'center']);
        $table->addCell(5000, ['valign' => 'center'])->addText("Deskripsi Kegiatan", ['name' => 'Times New Roman', 'size' => 12, 'bold' => true], ['align' => 'center']);
        $table->addCell(5000, ['valign' => 'center'])->addText("Foto", ['name' => 'Times New Roman', 'size' => 12, 'bold' => true], ['align' => 'center']);
        $table->addCell(2000, ['valign' => 'center'])->addText("Pembimbing Lapangan", ['name' => 'Times New Roman', 'size' => 12, 'bold' => true], ['align' => 'center']);
        $table->addCell(1500, ['valign' => 'center'])->addText("Tanda Tangan", ['name' => 'Times New Roman', 'size' => 12, 'bold' => true], ['align' => 'center']);
        $i = 1;
        foreach (Activity::where('tanggal', $item['tanggal'])->get()->toArray() as $activity) {
            $table->addRow();
            $table->addCell(400, ['valign' => 'center'])->addText($i, ['name' => 'Times New Roman', 'size' => 12], ['align' => 'center']);
            $table->addCell(1000, ['valign' => 'center'])->addText($activity['mulai'] . '-' . $activity['akhir'], ['name' => 'Times New Roman', 'size' => 12], ['align' => 'center']);
            $table->addCell(2000, ['valign' => 'center'])->addText($activity['deskripsi'], ['name' => 'Times New Roman', 'size' => 12], ['align' => 'center']);
            if ($activity['img']) {
                $table->addCell(2000, ['valign' => 'center'])->addImage('/public/upload/' . $activity['img'], ['alignment' => 'center']);
            } else {
                $table->addCell(2000, ['valign' => 'center'])->addText("", ['name' => 'Times New Roman', 'size' => 12], ['align' => 'center']);
            }
            $table->addCell(1000, ['valign' => 'center'])->addText("Dr. Deni Darmawan,S.Pd.,M.Si.", ['name' => 'Times New Roman', 'size' => 12], ['align' => 'center']);
            $table->addCell(1000, ['valign' => 'center'])->addText("", ['name' => 'Times New Roman', 'size' => 12], ['align' => 'center']);
            $i++;
            $section->addTextBreak();
        }
        $section->addPageBreak();
    }
    $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($word, 'Word2007');
    $name_gen = time() . '.docx';
    $objWriter->save($name_gen);
    return redirect($name_gen);
});

$router->get('/user', function () {
    return response()->json(['data' => User::all()]);
});

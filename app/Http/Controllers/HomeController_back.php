<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;

class HomeController extends Controller
{
    //

    public function first($dateStart = null, $dateEnd = null ){
        //
        //$now = Carbon::today()->toDateString();
        $link = url()->current();
        $urls = explode("/",$link);
        $sDate = date("Y-m-d", strtotime($urls[5]));
        $eDate = date("Y-m-d", strtotime($urls[6]));

        $data = db::connection('mysql')
            ->table('skp')
            ->select(
               'skp.tanggalentri as tanggal_entri',
               'skp.tglbayar as tanggal_bayar',
               //'skp.nomor_skprd as nomor_skprd',
               //'skp.dataentri as data_entri',
               'npwpd.namawp as wajib_pajak',
               'npwpd.alamatwp as alamat_wajib_pajak',
               //'sptpd.keteranganpajak as uraian',
               //'sptpd.jumlahpajak as ketetapan',
               'payment.total as terbayar',
               //'skp.penyetor as penyetor',
               'skp.masa1 as masa_1',
               'skp.masa2 as masa_2',
               'sptpd.jenispajak',
               //'skp.lunas as lunas',
               //'tarif_dasar_pajak.noid as tdp_id',
               'tarif_dasar_pajak.obyekpajak as nama_rekening',
               'npwpd.Status_izin as status_izin'
               //'tarif_dasar_pajak.rekeninginduk as kode_rekening'
            )
            ->leftjoin('payment','payment.pengesahan', '=', 'skp.pengesahan')
            ->leftjoin('sptpd','sptpd.noid', '=', 'skp.nomor_sptpd')
            ->leftjoin('tarif_dasar_pajak','sptpd.obyekpajak', '=', 'tarif_dasar_pajak.noid')
            ->leftjoin('npwpd','sptpd.npwpd', '=', 'npwpd.npwpd')
            ->where([
                ['skp.keterangan','=','0'],
                ['skp.aktif','=','1'],
            ])
            ->wherebetween('skp.tanggalentri',[$sDate,$eDate])
            ->get();
            //dd($data);

        //echo $sDate;
        return response()->json($data);
    }

    public function IRpdl(){

        $retribusi = DB::table('rekap_per_rekening')->get();
        //dd($retribusi);
        return response()->json($retribusi);
    }

    public function index(){

        $success = 'Success';
        return response()->json($success);
    }

    public function IRpbb(){

        $now = Carbon::today()->toDateString();

        $jatibanteng = DB::connection('oracle')

            ->table('sppt as s')
            ->select(array(
                DB::raw("'JATIBANTENG' as kecamatan"),
                DB::raw('count(s.kd_propinsi) as sppt_ketetapan'),
                DB::raw('sum(s.pbb_yg_harus_dibayar_sppt) as nom_ketetapan'),
                DB::raw('(select SUM(STTS)
                    from (SELECT   kd_propinsi,
                    kd_dati2,
                    kd_kecamatan,
                    TRUNC (tgl_pembayaran_sppt) AS tanggal,
                    COUNT ( * ) AS stts,
                    SUM (jml_sppt_yg_dibayar - denda_sppt) AS pokok,
                    SUM (denda_sppt) AS denda,
                    SUM (jml_sppt_yg_dibayar) AS jumlah
                    FROM   pembayaran_sppt
                    WHERE PEMBAYARAN_SPPT.THN_PAJAK_SPPT = extract(YEAR from sysdate)
                    GROUP BY   kd_propinsi, kd_dati2, kd_kecamatan, TRUNC (tgl_pembayaran_sppt))
                    where KD_KECAMATAN=020) as sppt_terbayar'),
                DB::raw('(select sum(POKOK)
                   from (SELECT   kd_propinsi,
                   kd_dati2,
                   kd_kecamatan,
                   TRUNC (tgl_pembayaran_sppt) AS tanggal,
                   COUNT ( * ) AS stts,
                   SUM (jml_sppt_yg_dibayar - denda_sppt) AS pokok,
                   SUM (denda_sppt) AS denda,
                   SUM (jml_sppt_yg_dibayar) AS jumlah
                   FROM   pembayaran_sppt
                   WHERE PEMBAYARAN_SPPT.THN_PAJAK_SPPT = extract(YEAR from sysdate)
                   GROUP BY   kd_propinsi, kd_dati2, kd_kecamatan, TRUNC (tgl_pembayaran_sppt))
                   where KD_KECAMATAN=020) as nom_terbayar')
            ))
            ->where('s.thn_pajak_sppt','=','2019')
            ->where('s.kd_kecamatan','=','020')
            ->where('s.kd_jns_op','=','0')
            ->groupby('s.thn_pajak_sppt')
            ->groupby('s.kd_kecamatan');

        $besuki = DB::connection('oracle')

            ->table('sppt as s')
            ->select(array(
                DB::raw("'BESUKI' as kecamatan"),
                DB::raw('count(s.kd_propinsi) as sppt_ketetapan'),
                DB::raw('sum(s.pbb_yg_harus_dibayar_sppt) as nom_ketetapan'),
                DB::raw('(select SUM(STTS)
                    from (SELECT   kd_propinsi,
                    kd_dati2,
                    kd_kecamatan,
                    TRUNC (tgl_pembayaran_sppt) AS tanggal,
                    COUNT ( * ) AS stts,
                    SUM (jml_sppt_yg_dibayar - denda_sppt) AS pokok,
                    SUM (denda_sppt) AS denda,
                    SUM (jml_sppt_yg_dibayar) AS jumlah
                    FROM   pembayaran_sppt
                    WHERE PEMBAYARAN_SPPT.THN_PAJAK_SPPT = extract(YEAR from sysdate)
                    GROUP BY   kd_propinsi, kd_dati2, kd_kecamatan, TRUNC (tgl_pembayaran_sppt))
                    where KD_KECAMATAN=030) as sppt_terbayar'),
                DB::raw('(select sum(POKOK)
                   from (SELECT   kd_propinsi,
                   kd_dati2,
                   kd_kecamatan,
                   TRUNC (tgl_pembayaran_sppt) AS tanggal,
                   COUNT ( * ) AS stts,
                   SUM (jml_sppt_yg_dibayar - denda_sppt) AS pokok,
                   SUM (denda_sppt) AS denda,
                   SUM (jml_sppt_yg_dibayar) AS jumlah
                   FROM   pembayaran_sppt
                   WHERE PEMBAYARAN_SPPT.THN_PAJAK_SPPT = extract(YEAR from sysdate)
                   GROUP BY   kd_propinsi, kd_dati2, kd_kecamatan, TRUNC (tgl_pembayaran_sppt))
                   where KD_KECAMATAN=030) as nom_terbayar')
            ))
            ->where('s.thn_pajak_sppt','=','2019')
            ->where('s.kd_kecamatan','=','030')
            ->where('s.kd_jns_op','=','0')
            ->groupby('s.thn_pajak_sppt')
            ->groupby('s.kd_kecamatan');

        $banyuglugur = DB::connection('oracle')

            ->table('sppt as s')
            ->select(array(
                DB::raw("'BANYUGLUGUR' as kecamatan"),
                DB::raw('count(s.kd_propinsi) as sppt_ketetapan'),
                DB::raw('sum(s.pbb_yg_harus_dibayar_sppt) as nom_ketetapan'),
                DB::raw('(select SUM(STTS)
                    from (SELECT   kd_propinsi,
                    kd_dati2,
                    kd_kecamatan,
                    TRUNC (tgl_pembayaran_sppt) AS tanggal,
                    COUNT ( * ) AS stts,
                    SUM (jml_sppt_yg_dibayar - denda_sppt) AS pokok,
                    SUM (denda_sppt) AS denda,
                    SUM (jml_sppt_yg_dibayar) AS jumlah
                    FROM   pembayaran_sppt
                    WHERE PEMBAYARAN_SPPT.THN_PAJAK_SPPT = extract(YEAR from sysdate)
                    GROUP BY   kd_propinsi, kd_dati2, kd_kecamatan, TRUNC (tgl_pembayaran_sppt))
                    where KD_KECAMATAN=031) as sppt_terbayar'),
                DB::raw('(select sum(POKOK)
                   from (SELECT   kd_propinsi,
                   kd_dati2,
                   kd_kecamatan,
                   TRUNC (tgl_pembayaran_sppt) AS tanggal,
                   COUNT ( * ) AS stts,
                   SUM (jml_sppt_yg_dibayar - denda_sppt) AS pokok,
                   SUM (denda_sppt) AS denda,
                   SUM (jml_sppt_yg_dibayar) AS jumlah
                   FROM   pembayaran_sppt
                   WHERE PEMBAYARAN_SPPT.THN_PAJAK_SPPT = extract(YEAR from sysdate)
                   GROUP BY   kd_propinsi, kd_dati2, kd_kecamatan, TRUNC (tgl_pembayaran_sppt))
                   where KD_KECAMATAN=031) as nom_terbayar')
            ))
            ->where('s.thn_pajak_sppt','=','2019')
            ->where('s.kd_kecamatan','=','031')
            ->where('s.kd_jns_op','=','0')
            ->groupby('s.thn_pajak_sppt')
            ->groupby('s.kd_kecamatan');

        $suboh = DB::connection('oracle')

            ->table('sppt as s')
            ->select(array(
                DB::raw("'SUBOH' as kecamatan"),
                DB::raw('count(s.kd_propinsi) as sppt_ketetapan'),
                DB::raw('sum(s.pbb_yg_harus_dibayar_sppt) as nom_ketetapan'),
                DB::raw('(select SUM(STTS)
                    from (SELECT   kd_propinsi,
                    kd_dati2,
                    kd_kecamatan,
                    TRUNC (tgl_pembayaran_sppt) AS tanggal,
                    COUNT ( * ) AS stts,
                    SUM (jml_sppt_yg_dibayar - denda_sppt) AS pokok,
                    SUM (denda_sppt) AS denda,
                    SUM (jml_sppt_yg_dibayar) AS jumlah
                    FROM   pembayaran_sppt
                    WHERE PEMBAYARAN_SPPT.THN_PAJAK_SPPT = extract(YEAR from sysdate)
                    GROUP BY   kd_propinsi, kd_dati2, kd_kecamatan, TRUNC (tgl_pembayaran_sppt))
                    where KD_KECAMATAN=040) as sppt_terbayar'),
                DB::raw('(select sum(POKOK)
                   from (SELECT   kd_propinsi,
                   kd_dati2,
                   kd_kecamatan,
                   TRUNC (tgl_pembayaran_sppt) AS tanggal,
                   COUNT ( * ) AS stts,
                   SUM (jml_sppt_yg_dibayar - denda_sppt) AS pokok,
                   SUM (denda_sppt) AS denda,
                   SUM (jml_sppt_yg_dibayar) AS jumlah
                   FROM   pembayaran_sppt
                   WHERE PEMBAYARAN_SPPT.THN_PAJAK_SPPT = extract(YEAR from sysdate)
                   GROUP BY   kd_propinsi, kd_dati2, kd_kecamatan, TRUNC (tgl_pembayaran_sppt))
                   where KD_KECAMATAN=040) as nom_terbayar')
            ))
            ->where('s.thn_pajak_sppt','=','2019')
            ->where('s.kd_kecamatan','=','040')
            ->where('s.kd_jns_op','=','0')
            ->groupby('s.thn_pajak_sppt')
            ->groupby('s.kd_kecamatan');

        $mlandingan = DB::connection('oracle')

            ->table('sppt as s')
            ->select(array(
                DB::raw("'MLANDINGAN' as kecamatan"),
                DB::raw('count(s.kd_propinsi) as sppt_ketetapan'),
                DB::raw('sum(s.pbb_yg_harus_dibayar_sppt) as nom_ketetapan'),
                DB::raw('(select SUM(STTS)
                    from (SELECT   kd_propinsi,
                    kd_dati2,
                    kd_kecamatan,
                    TRUNC (tgl_pembayaran_sppt) AS tanggal,
                    COUNT ( * ) AS stts,
                    SUM (jml_sppt_yg_dibayar - denda_sppt) AS pokok,
                    SUM (denda_sppt) AS denda,
                    SUM (jml_sppt_yg_dibayar) AS jumlah
                    FROM   pembayaran_sppt
                    WHERE PEMBAYARAN_SPPT.THN_PAJAK_SPPT = extract(YEAR from sysdate)
                    GROUP BY   kd_propinsi, kd_dati2, kd_kecamatan, TRUNC (tgl_pembayaran_sppt))
                    where KD_KECAMATAN=050) as sppt_terbayar'),
                DB::raw('(select sum(POKOK)
                   from (SELECT   kd_propinsi,
                   kd_dati2,
                   kd_kecamatan,
                   TRUNC (tgl_pembayaran_sppt) AS tanggal,
                   COUNT ( * ) AS stts,
                   SUM (jml_sppt_yg_dibayar - denda_sppt) AS pokok,
                   SUM (denda_sppt) AS denda,
                   SUM (jml_sppt_yg_dibayar) AS jumlah
                   FROM   pembayaran_sppt
                   WHERE PEMBAYARAN_SPPT.THN_PAJAK_SPPT = extract(YEAR from sysdate)
                   GROUP BY   kd_propinsi, kd_dati2, kd_kecamatan, TRUNC (tgl_pembayaran_sppt))
                   where KD_KECAMATAN=050) as nom_terbayar')
            ))
            ->where('s.thn_pajak_sppt','=','2019')
            ->where('s.kd_kecamatan','=','050')
            ->where('s.kd_jns_op','=','0')
            ->groupby('s.thn_pajak_sppt')
            ->groupby('s.kd_kecamatan');

        $bungatan = DB::connection('oracle')

            ->table('sppt as s')
            ->select(array(
                DB::raw("'BUNGATAN' as kecamatan"),
                DB::raw('count(s.kd_propinsi) as sppt_ketetapan'),
                DB::raw('sum(s.pbb_yg_harus_dibayar_sppt) as nom_ketetapan'),
                DB::raw('(select SUM(STTS)
                    from (SELECT   kd_propinsi,
                    kd_dati2,
                    kd_kecamatan,
                    TRUNC (tgl_pembayaran_sppt) AS tanggal,
                    COUNT ( * ) AS stts,
                    SUM (jml_sppt_yg_dibayar - denda_sppt) AS pokok,
                    SUM (denda_sppt) AS denda,
                    SUM (jml_sppt_yg_dibayar) AS jumlah
                    FROM   pembayaran_sppt
                    WHERE PEMBAYARAN_SPPT.THN_PAJAK_SPPT = extract(YEAR from sysdate)
                    GROUP BY   kd_propinsi, kd_dati2, kd_kecamatan, TRUNC (tgl_pembayaran_sppt))
                    where KD_KECAMATAN=051) as sppt_terbayar'),
                DB::raw('(select sum(POKOK)
                   from (SELECT   kd_propinsi,
                   kd_dati2,
                   kd_kecamatan,
                   TRUNC (tgl_pembayaran_sppt) AS tanggal,
                   COUNT ( * ) AS stts,
                   SUM (jml_sppt_yg_dibayar - denda_sppt) AS pokok,
                   SUM (denda_sppt) AS denda,
                   SUM (jml_sppt_yg_dibayar) AS jumlah
                   FROM   pembayaran_sppt
                   WHERE PEMBAYARAN_SPPT.THN_PAJAK_SPPT = extract(YEAR from sysdate)
                   GROUP BY   kd_propinsi, kd_dati2, kd_kecamatan, TRUNC (tgl_pembayaran_sppt))
                   where KD_KECAMATAN=051) as nom_terbayar')
            ))
            ->where('s.thn_pajak_sppt','=','2019')
            ->where('s.kd_kecamatan','=','051')
            ->where('s.kd_jns_op','=','0')
            ->groupby('s.thn_pajak_sppt')
            ->groupby('s.kd_kecamatan');

        $kendit = DB::connection('oracle')

            ->table('sppt as s')
            ->select(array(
                DB::raw("'KENDIT' as kecamatan"),
                DB::raw('count(s.kd_propinsi) as sppt_ketetapan'),
                DB::raw('sum(s.pbb_yg_harus_dibayar_sppt) as nom_ketetapan'),
                DB::raw('(select SUM(STTS)
                    from (SELECT   kd_propinsi,
                    kd_dati2,
                    kd_kecamatan,
                    TRUNC (tgl_pembayaran_sppt) AS tanggal,
                    COUNT ( * ) AS stts,
                    SUM (jml_sppt_yg_dibayar - denda_sppt) AS pokok,
                    SUM (denda_sppt) AS denda,
                    SUM (jml_sppt_yg_dibayar) AS jumlah
                    FROM   pembayaran_sppt
                    WHERE PEMBAYARAN_SPPT.THN_PAJAK_SPPT = extract(YEAR from sysdate)
                    GROUP BY   kd_propinsi, kd_dati2, kd_kecamatan, TRUNC (tgl_pembayaran_sppt))
                    where KD_KECAMATAN=060) as sppt_terbayar'),
                DB::raw('(select sum(POKOK)
                   from (SELECT   kd_propinsi,
                   kd_dati2,
                   kd_kecamatan,
                   TRUNC (tgl_pembayaran_sppt) AS tanggal,
                   COUNT ( * ) AS stts,
                   SUM (jml_sppt_yg_dibayar - denda_sppt) AS pokok,
                   SUM (denda_sppt) AS denda,
                   SUM (jml_sppt_yg_dibayar) AS jumlah
                   FROM   pembayaran_sppt
                   WHERE PEMBAYARAN_SPPT.THN_PAJAK_SPPT = extract(YEAR from sysdate)
                   GROUP BY   kd_propinsi, kd_dati2, kd_kecamatan, TRUNC (tgl_pembayaran_sppt))
                   where KD_KECAMATAN=060) as nom_terbayar')
            ))
            ->where('s.thn_pajak_sppt','=','2019')
            ->where('s.kd_kecamatan','=','060')
            ->where('s.kd_jns_op','=','0')
            ->groupby('s.thn_pajak_sppt')
            ->groupby('s.kd_kecamatan');

        $panarukan = DB::connection('oracle')

            ->table('sppt as s')
            ->select(array(
                DB::raw("'PANARUKAN' as kecamatan"),
                DB::raw('count(s.kd_propinsi) as sppt_ketetapan'),
                DB::raw('sum(s.pbb_yg_harus_dibayar_sppt) as nom_ketetapan'),
                DB::raw('(select SUM(STTS)
                    from (SELECT   kd_propinsi,
                    kd_dati2,
                    kd_kecamatan,
                    TRUNC (tgl_pembayaran_sppt) AS tanggal,
                    COUNT ( * ) AS stts,
                    SUM (jml_sppt_yg_dibayar - denda_sppt) AS pokok,
                    SUM (denda_sppt) AS denda,
                    SUM (jml_sppt_yg_dibayar) AS jumlah
                    FROM   pembayaran_sppt
                    WHERE PEMBAYARAN_SPPT.THN_PAJAK_SPPT = extract(YEAR from sysdate)
                    GROUP BY   kd_propinsi, kd_dati2, kd_kecamatan, TRUNC (tgl_pembayaran_sppt))
                    where KD_KECAMATAN=070) as sppt_terbayar'),
                DB::raw('(select sum(POKOK)
                   from (SELECT   kd_propinsi,
                   kd_dati2,
                   kd_kecamatan,
                   TRUNC (tgl_pembayaran_sppt) AS tanggal,
                   COUNT ( * ) AS stts,
                   SUM (jml_sppt_yg_dibayar - denda_sppt) AS pokok,
                   SUM (denda_sppt) AS denda,
                   SUM (jml_sppt_yg_dibayar) AS jumlah
                   FROM   pembayaran_sppt
                   WHERE PEMBAYARAN_SPPT.THN_PAJAK_SPPT = extract(YEAR from sysdate)
                   GROUP BY   kd_propinsi, kd_dati2, kd_kecamatan, TRUNC (tgl_pembayaran_sppt))
                   where KD_KECAMATAN=070) as nom_terbayar')
            ))
            ->where('s.thn_pajak_sppt','=','2019')
            ->where('s.kd_kecamatan','=','070')
            ->where('s.kd_jns_op','=','0')
            ->groupby('s.thn_pajak_sppt')
            ->groupby('s.kd_kecamatan');

        $situbondo = DB::connection('oracle')

            ->table('sppt as s')
            ->select(array(
                DB::raw("'SITUBONDO' as kecamatan"),
                DB::raw('count(s.kd_propinsi) as sppt_ketetapan'),
                DB::raw('sum(s.pbb_yg_harus_dibayar_sppt) as nom_ketetapan'),
                DB::raw('(select SUM(STTS)
                    from (SELECT   kd_propinsi,
                    kd_dati2,
                    kd_kecamatan,
                    TRUNC (tgl_pembayaran_sppt) AS tanggal,
                    COUNT ( * ) AS stts,
                    SUM (jml_sppt_yg_dibayar - denda_sppt) AS pokok,
                    SUM (denda_sppt) AS denda,
                    SUM (jml_sppt_yg_dibayar) AS jumlah
                    FROM   pembayaran_sppt
                    WHERE PEMBAYARAN_SPPT.THN_PAJAK_SPPT = extract(YEAR from sysdate)
                    GROUP BY   kd_propinsi, kd_dati2, kd_kecamatan, TRUNC (tgl_pembayaran_sppt))
                    where KD_KECAMATAN=080) as sppt_terbayar'),
                DB::raw('(select sum(POKOK)
                   from (SELECT   kd_propinsi,
                   kd_dati2,
                   kd_kecamatan,
                   TRUNC (tgl_pembayaran_sppt) AS tanggal,
                   COUNT ( * ) AS stts,
                   SUM (jml_sppt_yg_dibayar - denda_sppt) AS pokok,
                   SUM (denda_sppt) AS denda,
                   SUM (jml_sppt_yg_dibayar) AS jumlah
                   FROM   pembayaran_sppt
                   WHERE PEMBAYARAN_SPPT.THN_PAJAK_SPPT = extract(YEAR from sysdate)
                   GROUP BY   kd_propinsi, kd_dati2, kd_kecamatan, TRUNC (tgl_pembayaran_sppt))
                   where KD_KECAMATAN=080) as nom_terbayar')
            ))
            ->where('s.thn_pajak_sppt','=','2019')
            ->where('s.kd_kecamatan','=','080')
            ->where('s.kd_jns_op','=','0')
            ->groupby('s.thn_pajak_sppt')
            ->groupby('s.kd_kecamatan');

        $mangaran = DB::connection('oracle')

            ->table('sppt as s')
            ->select(array(
                DB::raw("'MANGARAN' as kecamatan"),
                DB::raw('count(s.kd_propinsi) as sppt_ketetapan'),
                DB::raw('sum(s.pbb_yg_harus_dibayar_sppt) as nom_ketetapan'),
                DB::raw('(select SUM(STTS)
                    from (SELECT   kd_propinsi,
                    kd_dati2,
                    kd_kecamatan,
                    TRUNC (tgl_pembayaran_sppt) AS tanggal,
                    COUNT ( * ) AS stts,
                    SUM (jml_sppt_yg_dibayar - denda_sppt) AS pokok,
                    SUM (denda_sppt) AS denda,
                    SUM (jml_sppt_yg_dibayar) AS jumlah
                    FROM   pembayaran_sppt
                    WHERE PEMBAYARAN_SPPT.THN_PAJAK_SPPT = extract(YEAR from sysdate)
                    GROUP BY   kd_propinsi, kd_dati2, kd_kecamatan, TRUNC (tgl_pembayaran_sppt))
                    where KD_KECAMATAN=090) as sppt_terbayar'),
                DB::raw('(select sum(POKOK)
                   from (SELECT   kd_propinsi,
                   kd_dati2,
                   kd_kecamatan,
                   TRUNC (tgl_pembayaran_sppt) AS tanggal,
                   COUNT ( * ) AS stts,
                   SUM (jml_sppt_yg_dibayar - denda_sppt) AS pokok,
                   SUM (denda_sppt) AS denda,
                   SUM (jml_sppt_yg_dibayar) AS jumlah
                   FROM   pembayaran_sppt
                   WHERE PEMBAYARAN_SPPT.THN_PAJAK_SPPT = extract(YEAR from sysdate)
                   GROUP BY   kd_propinsi, kd_dati2, kd_kecamatan, TRUNC (tgl_pembayaran_sppt))
                   where KD_KECAMATAN=090) as nom_terbayar')
            ))
            ->where('s.thn_pajak_sppt','=','2019')
            ->where('s.kd_kecamatan','=','090')
            ->where('s.kd_jns_op','=','0')
            ->groupby('s.thn_pajak_sppt')
            ->groupby('s.kd_kecamatan');

        $panji = DB::connection('oracle')

            ->table('sppt as s')
            ->select(array(
                DB::raw("'PANJI' as kecamatan"),
                DB::raw('count(s.kd_propinsi) as sppt_ketetapan'),
                DB::raw('sum(s.pbb_yg_harus_dibayar_sppt) as nom_ketetapan'),
                DB::raw('(select SUM(STTS)
                    from (SELECT   kd_propinsi,
                    kd_dati2,
                    kd_kecamatan,
                    TRUNC (tgl_pembayaran_sppt) AS tanggal,
                    COUNT ( * ) AS stts,
                    SUM (jml_sppt_yg_dibayar - denda_sppt) AS pokok,
                    SUM (denda_sppt) AS denda,
                    SUM (jml_sppt_yg_dibayar) AS jumlah
                    FROM   pembayaran_sppt
                    WHERE PEMBAYARAN_SPPT.THN_PAJAK_SPPT = extract(YEAR from sysdate)
                    GROUP BY   kd_propinsi, kd_dati2, kd_kecamatan, TRUNC (tgl_pembayaran_sppt))
                    where KD_KECAMATAN=100) as sppt_terbayar'),
                DB::raw('(select sum(POKOK)
                   from (SELECT   kd_propinsi,
                   kd_dati2,
                   kd_kecamatan,
                   TRUNC (tgl_pembayaran_sppt) AS tanggal,
                   COUNT ( * ) AS stts,
                   SUM (jml_sppt_yg_dibayar - denda_sppt) AS pokok,
                   SUM (denda_sppt) AS denda,
                   SUM (jml_sppt_yg_dibayar) AS jumlah
                   FROM   pembayaran_sppt
                   WHERE PEMBAYARAN_SPPT.THN_PAJAK_SPPT = extract(YEAR from sysdate)
                   GROUP BY   kd_propinsi, kd_dati2, kd_kecamatan, TRUNC (tgl_pembayaran_sppt))
                   where KD_KECAMATAN=100) as nom_terbayar')
            ))
            ->where('s.thn_pajak_sppt','=','2019')
            ->where('s.kd_kecamatan','=','100')
            ->where('s.kd_jns_op','=','0')
            ->groupby('s.thn_pajak_sppt')
            ->groupby('s.kd_kecamatan');

        $kapongan = DB::connection('oracle')

            ->table('sppt as s')
            ->select(array(
                DB::raw("'KAPONGAN' as kecamatan"),
                DB::raw('count(s.kd_propinsi) as sppt_ketetapan'),
                DB::raw('sum(s.pbb_yg_harus_dibayar_sppt) as nom_ketetapan'),
                DB::raw('(select SUM(STTS)
                    from (SELECT   kd_propinsi,
                    kd_dati2,
                    kd_kecamatan,
                    TRUNC (tgl_pembayaran_sppt) AS tanggal,
                    COUNT ( * ) AS stts,
                    SUM (jml_sppt_yg_dibayar - denda_sppt) AS pokok,
                    SUM (denda_sppt) AS denda,
                    SUM (jml_sppt_yg_dibayar) AS jumlah
                    FROM   pembayaran_sppt
                    WHERE PEMBAYARAN_SPPT.THN_PAJAK_SPPT = extract(YEAR from sysdate)
                    GROUP BY   kd_propinsi, kd_dati2, kd_kecamatan, TRUNC (tgl_pembayaran_sppt))
                    where KD_KECAMATAN=110) as sppt_terbayar'),
                DB::raw('(select sum(POKOK)
                   from (SELECT   kd_propinsi,
                   kd_dati2,
                   kd_kecamatan,
                   TRUNC (tgl_pembayaran_sppt) AS tanggal,
                   COUNT ( * ) AS stts,
                   SUM (jml_sppt_yg_dibayar - denda_sppt) AS pokok,
                   SUM (denda_sppt) AS denda,
                   SUM (jml_sppt_yg_dibayar) AS jumlah
                   FROM   pembayaran_sppt
                   WHERE PEMBAYARAN_SPPT.THN_PAJAK_SPPT = extract(YEAR from sysdate)
                   GROUP BY   kd_propinsi, kd_dati2, kd_kecamatan, TRUNC (tgl_pembayaran_sppt))
                   where KD_KECAMATAN=110) as nom_terbayar')
            ))
            ->where('s.thn_pajak_sppt','=','2019')
            ->where('s.kd_kecamatan','=','110')
            ->where('s.kd_jns_op','=','0')
            ->groupby('s.thn_pajak_sppt')
            ->groupby('s.kd_kecamatan');

        $sumbermalang = DB::connection('oracle')

            ->table('sppt as s')
            ->select(array(
                DB::raw("'SUMBERMALANG' as kecamatan"),
                DB::raw('count(s.kd_propinsi) as sppt_ketetapan'),
                DB::raw('sum(s.pbb_yg_harus_dibayar_sppt) as nom_ketetapan'),
                DB::raw('(select SUM(STTS)
                    from (SELECT   kd_propinsi,
                    kd_dati2,
                    kd_kecamatan,
                    TRUNC (tgl_pembayaran_sppt) AS tanggal,
                    COUNT ( * ) AS stts,
                    SUM (jml_sppt_yg_dibayar - denda_sppt) AS pokok,
                    SUM (denda_sppt) AS denda,
                    SUM (jml_sppt_yg_dibayar) AS jumlah
                    FROM   pembayaran_sppt
                    WHERE PEMBAYARAN_SPPT.THN_PAJAK_SPPT = extract(YEAR from sysdate)
                    GROUP BY   kd_propinsi, kd_dati2, kd_kecamatan, TRUNC (tgl_pembayaran_sppt))
                    where KD_KECAMATAN=111) as sppt_terbayar'),
                DB::raw('(select sum(POKOK)
                   from (SELECT   kd_propinsi,
                   kd_dati2,
                   kd_kecamatan,
                   TRUNC (tgl_pembayaran_sppt) AS tanggal,
                   COUNT ( * ) AS stts,
                   SUM (jml_sppt_yg_dibayar - denda_sppt) AS pokok,
                   SUM (denda_sppt) AS denda,
                   SUM (jml_sppt_yg_dibayar) AS jumlah
                   FROM   pembayaran_sppt
                   WHERE PEMBAYARAN_SPPT.THN_PAJAK_SPPT = extract(YEAR from sysdate)
                   GROUP BY   kd_propinsi, kd_dati2, kd_kecamatan, TRUNC (tgl_pembayaran_sppt))
                   where KD_KECAMATAN=111) as nom_terbayar')
            ))
            ->where('s.thn_pajak_sppt','=','2019')
            ->where('s.kd_kecamatan','=','111')
            ->where('s.kd_jns_op','=','0')
            ->groupby('s.thn_pajak_sppt')
            ->groupby('s.kd_kecamatan');

        $arjasa = DB::connection('oracle')

            ->table('sppt as s')
            ->select(array(
                DB::raw("'ARJASA' as kecamatan"),
                DB::raw('count(s.kd_propinsi) as sppt_ketetapan'),
                DB::raw('sum(s.pbb_yg_harus_dibayar_sppt) as nom_ketetapan'),
                DB::raw('(select SUM(STTS)
                    from (SELECT   kd_propinsi,
                    kd_dati2,
                    kd_kecamatan,
                    TRUNC (tgl_pembayaran_sppt) AS tanggal,
                    COUNT ( * ) AS stts,
                    SUM (jml_sppt_yg_dibayar - denda_sppt) AS pokok,
                    SUM (denda_sppt) AS denda,
                    SUM (jml_sppt_yg_dibayar) AS jumlah
                    FROM   pembayaran_sppt
                    WHERE PEMBAYARAN_SPPT.THN_PAJAK_SPPT = extract(YEAR from sysdate)
                    GROUP BY   kd_propinsi, kd_dati2, kd_kecamatan, TRUNC (tgl_pembayaran_sppt))
                    where KD_KECAMATAN=120) as sppt_terbayar'),
                DB::raw('(select sum(POKOK)
                   from (SELECT   kd_propinsi,
                   kd_dati2,
                   kd_kecamatan,
                   TRUNC (tgl_pembayaran_sppt) AS tanggal,
                   COUNT ( * ) AS stts,
                   SUM (jml_sppt_yg_dibayar - denda_sppt) AS pokok,
                   SUM (denda_sppt) AS denda,
                   SUM (jml_sppt_yg_dibayar) AS jumlah
                   FROM   pembayaran_sppt
                   WHERE PEMBAYARAN_SPPT.THN_PAJAK_SPPT = extract(YEAR from sysdate)
                   GROUP BY   kd_propinsi, kd_dati2, kd_kecamatan, TRUNC (tgl_pembayaran_sppt))
                   where KD_KECAMATAN=120) as nom_terbayar')
            ))
            ->where('s.thn_pajak_sppt','=','2019')
            ->where('s.kd_kecamatan','=','120')
            ->where('s.kd_jns_op','=','0')
            ->groupby('s.thn_pajak_sppt')
            ->groupby('s.kd_kecamatan');

        $jangkar = DB::connection('oracle')

            ->table('sppt as s')
            ->select(array(
                DB::raw("'JANGKAR' as kecamatan"),
                DB::raw('count(s.kd_propinsi) as sppt_ketetapan'),
                DB::raw('sum(s.pbb_yg_harus_dibayar_sppt) as nom_ketetapan'),
                DB::raw('(select SUM(STTS)
                    from (SELECT   kd_propinsi,
                    kd_dati2,
                    kd_kecamatan,
                    TRUNC (tgl_pembayaran_sppt) AS tanggal,
                    COUNT ( * ) AS stts,
                    SUM (jml_sppt_yg_dibayar - denda_sppt) AS pokok,
                    SUM (denda_sppt) AS denda,
                    SUM (jml_sppt_yg_dibayar) AS jumlah
                    FROM   pembayaran_sppt
                    WHERE PEMBAYARAN_SPPT.THN_PAJAK_SPPT = extract(YEAR from sysdate)
                    GROUP BY   kd_propinsi, kd_dati2, kd_kecamatan, TRUNC (tgl_pembayaran_sppt))
                    where KD_KECAMATAN=130) as sppt_terbayar'),
                DB::raw('(select sum(POKOK)
                   from (SELECT   kd_propinsi,
                   kd_dati2,
                   kd_kecamatan,
                   TRUNC (tgl_pembayaran_sppt) AS tanggal,
                   COUNT ( * ) AS stts,
                   SUM (jml_sppt_yg_dibayar - denda_sppt) AS pokok,
                   SUM (denda_sppt) AS denda,
                   SUM (jml_sppt_yg_dibayar) AS jumlah
                   FROM   pembayaran_sppt
                   WHERE PEMBAYARAN_SPPT.THN_PAJAK_SPPT = extract(YEAR from sysdate)
                   GROUP BY   kd_propinsi, kd_dati2, kd_kecamatan, TRUNC (tgl_pembayaran_sppt))
                   where KD_KECAMATAN=130) as nom_terbayar')
            ))
            ->where('s.thn_pajak_sppt','=','2019')
            ->where('s.kd_kecamatan','=','130')
            ->where('s.kd_jns_op','=','0')
            ->groupby('s.thn_pajak_sppt')
            ->groupby('s.kd_kecamatan');

        $asembagus = DB::connection('oracle')

            ->table('sppt as s')
            ->select(array(
                DB::raw("'ASEMBAGUS' as kecamatan"),
                DB::raw('count(s.kd_propinsi) as sppt_ketetapan'),
                DB::raw('sum(s.pbb_yg_harus_dibayar_sppt) as nom_ketetapan'),
                DB::raw('(select SUM(STTS)
                    from (SELECT   kd_propinsi,
                    kd_dati2,
                    kd_kecamatan,
                    TRUNC (tgl_pembayaran_sppt) AS tanggal,
                    COUNT ( * ) AS stts,
                    SUM (jml_sppt_yg_dibayar - denda_sppt) AS pokok,
                    SUM (denda_sppt) AS denda,
                    SUM (jml_sppt_yg_dibayar) AS jumlah
                    FROM   pembayaran_sppt
                    WHERE PEMBAYARAN_SPPT.THN_PAJAK_SPPT = extract(YEAR from sysdate)
                    GROUP BY   kd_propinsi, kd_dati2, kd_kecamatan, TRUNC (tgl_pembayaran_sppt))
                    where KD_KECAMATAN=140) as sppt_terbayar'),
                DB::raw('(select sum(POKOK)
                   from (SELECT   kd_propinsi,
                   kd_dati2,
                   kd_kecamatan,
                   TRUNC (tgl_pembayaran_sppt) AS tanggal,
                   COUNT ( * ) AS stts,
                   SUM (jml_sppt_yg_dibayar - denda_sppt) AS pokok,
                   SUM (denda_sppt) AS denda,
                   SUM (jml_sppt_yg_dibayar) AS jumlah
                   FROM   pembayaran_sppt
                   WHERE PEMBAYARAN_SPPT.THN_PAJAK_SPPT = extract(YEAR from sysdate)
                   GROUP BY   kd_propinsi, kd_dati2, kd_kecamatan, TRUNC (tgl_pembayaran_sppt))
                   where KD_KECAMATAN=140) as nom_terbayar')
            ))
            ->where('s.thn_pajak_sppt','=','2019')
            ->where('s.kd_kecamatan','=','140')
            ->where('s.kd_jns_op','=','0')
            ->groupby('s.thn_pajak_sppt')
            ->groupby('s.kd_kecamatan');

        $banyuputih = DB::connection('oracle')

            ->table('sppt as s')
            ->select(array(
                DB::raw("'BANYUPUTIH' as kecamatan"),
                DB::raw('count(s.kd_propinsi) as sppt_ketetapan'),
                DB::raw('sum(s.pbb_yg_harus_dibayar_sppt) as nom_ketetapan'),
                DB::raw('(select SUM(STTS)
                    from (SELECT   kd_propinsi,
                    kd_dati2,
                    kd_kecamatan,
                    TRUNC (tgl_pembayaran_sppt) AS tanggal,
                    COUNT ( * ) AS stts,
                    SUM (jml_sppt_yg_dibayar - denda_sppt) AS pokok,
                    SUM (denda_sppt) AS denda,
                    SUM (jml_sppt_yg_dibayar) AS jumlah
                    FROM   pembayaran_sppt
                    WHERE PEMBAYARAN_SPPT.THN_PAJAK_SPPT = extract(YEAR from sysdate)
                    GROUP BY   kd_propinsi, kd_dati2, kd_kecamatan, TRUNC (tgl_pembayaran_sppt))
                    where KD_KECAMATAN=150) as sppt_terbayar'),
                DB::raw('(select sum(POKOK)
                   from (SELECT   kd_propinsi,
                   kd_dati2,
                   kd_kecamatan,
                   TRUNC (tgl_pembayaran_sppt) AS tanggal,
                   COUNT ( * ) AS stts,
                   SUM (jml_sppt_yg_dibayar - denda_sppt) AS pokok,
                   SUM (denda_sppt) AS denda,
                   SUM (jml_sppt_yg_dibayar) AS jumlah
                   FROM   pembayaran_sppt
                   WHERE PEMBAYARAN_SPPT.THN_PAJAK_SPPT = extract(YEAR from sysdate)
                   GROUP BY   kd_propinsi, kd_dati2, kd_kecamatan, TRUNC (tgl_pembayaran_sppt))
                   where KD_KECAMATAN=150) as nom_terbayar')
            ))
            ->where('s.thn_pajak_sppt','=','2019')
            ->where('s.kd_kecamatan','=','150')
            ->where('s.kd_jns_op','=','0')
            ->groupby('s.thn_pajak_sppt')
            ->groupby('s.kd_kecamatan')
            ->union($jatibanteng)
            ->union($besuki)
            ->union($banyuglugur)
            ->union($suboh)
            ->union($mlandingan)
            ->union($bungatan)
            ->union($kendit)
            ->union($panarukan)
            ->union($situbondo)
            ->union($mangaran)
            ->union($panji)
            ->union($kapongan)
            ->union($sumbermalang)
            ->union($arjasa)
            ->union($jangkar)
            ->union($asembagus)
            ->get();
            //->toSql();
        //dd($hasil);
        //return response()->json($hasil);
        return response()->json($banyuputih);
    }

    public function IRbphtb($pilihan){

        if($pilihan == 'kecamatan'){
            $result = DB::connection('mysql2')
                ->table('rekap_kecamatan')
                ->get();
                //dd($result[0]);
                //var_dump($result);
                //var_dump($result->getBindings());
        }elseif($pilihan == 'ppat'){
            $result = DB::connection('mysql2')
                ->table('rekap_ppat')
                ->get();
                //dd($result[0]);
                //var_dump($result);
                //var_dump($result->getBindings());
        }else{
            $result = 'sorry, nothing to show';
        }
        return response()->json($result);
    }
}

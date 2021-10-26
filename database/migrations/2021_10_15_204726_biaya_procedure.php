<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class BiayaProcedure extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // $procedure = "DROP FUNCTION IF EXISTS biaya_grid;
        // CREATE FUNCTION biaya_grid( p_biaya_id integer, p_pegawai_id integer)
        // RETURNS TABLE(reference_id bigint, modul text, tipe text, nama varchar, tanggal date, keterangan text, biaya numeric, remark varchar, created timestamp, file text)
        // AS $$
        // BEGIN
        //     return query select * from 
        //     ( 
        //       select p.id,
        //         'PENGELUARAN',
        //         'lainnya' as jenis, 
        //         kategori, 
        //         tgl as tanggal, jml || ' ' || satuan as jml, 
        //         total, 
        //         catatan, 
        //         p.created_at,
        //         f.file_path ||'/' ||f.file_name 
        //       from pengeluaran p 
        //       left join files as f
        //         on f.id = p.file_id
        //       where deleted_at is null
        //       and biaya_id = p_biaya_id
        //       and pegawai_id = p_pegawai_id
        //       union all 
        //       select i.id,
        //         'INAP',
        //         'Penginapan', 
        //         hotel, 
        //         tgl_checkin, 
        //         jml_hari || ' Hari', 
        //         total_bayar, 
        //         catatan, 
        //         i.created_at,
        //         f.file_path ||'/' ||f.file_name 
        //       from inap i 
        //       left join files as f
        //         on f.id = i.file_id
        //       where deleted_at is null 
        //       and biaya_id = p_biaya_id
        //       and pegawai_id = p_pegawai_id
        //       union all 
        //       select t.id,
        //         'TRANSPORT',
        //         jenis_transport, 
        //         agen, 
        //         tgl, 
        //         perjalanan, 
        //         total_bayar, 
        //         catatan, 
        //         t.created_at,
        //         f.file_path ||'/' ||f.file_name 
        //       from transport t
        //       left join files as f
        //         on f.id = t.file_id
        //       where deleted_at is null
        //       and biaya_id = p_biaya_id
        //       and pegawai_id = p_pegawai_id 
        //     ) a
        //     order by a.created_at desc;
        // end;
        // $$LANGUAGE plpgsql;";
  
        // \DB::unprepared($procedure);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}

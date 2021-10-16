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
        $procedure = "DROP FUNCTION IF EXISTS biaya_grid;
            CREATE FUNCTION biaya_grid( p_biaya_id integer, p_pegawai_id integer)
            RETURNS TABLE(tipe text, nama varchar, tanggal date, qty text, biaya numeric, cttn varchar, created timestamp)
            AS $$
            BEGIN
                return query select * from ( select 'lainnya' as jenis, jenis_pengeluaran, tgl as tanggal, jml || ' ' || jenis_satuan as jml, total_biaya, catatan as cttn, created_at from pengeluaran p 
                where deleted_at is null
                and biaya_id = p_biaya_id
                and pegawai_id = p_pegawai_id
                union all 
                select 'Penginapan', hotel, tgl_checkin, jml_hari || ' Hari', total_bayar, catatan, created_at  from inap i 
                where deleted_at is null 
                and biaya_id = p_biaya_id
                and pegawai_id = p_pegawai_id
                union all 
                select jenis_transport, agen, tgl, perjalanan, total_bayar, catatan, created_at from transport t 
                where deleted_at is null
                and biaya_id = p_biaya_id
                and pegawai_id = p_pegawai_id ) a
                order by a.created_at desc;
            end;
            $$LANGUAGE plpgsql;";
  
        \DB::unprepared($procedure);
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

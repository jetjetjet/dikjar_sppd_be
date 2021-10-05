<table>
  <thead>
    <tr>
      <th colspan=40> REKAPITULASI PERJALANAN DINAS LUAR DAERAH DAN DALAM DAERAH (LUAR KABUPATEN KERINCI) </th>
    </tr>
    <tr>
      <th colspan=40> PERIODE:  1  Januari 2020 S.D  31  Desember 2020 </th>
    </tr>
    <tr>
      <th colspan=40> PADA BADAN KEPEGAWAIAN DAN PENGEMBANGAN SUMBER DAYA MANUSIA DAERAH </th>
    </tr>
    <tr>
      <th colspan=40> </th>
    </tr>
    <tr> 
      <th class="align-middle" rowspan=2> No. </th>
      <th class="align-middle" colspan=2> Pelaksana </th>
      <th class="align-middle" rowspan=2> No. BKU </th>
      <th class="align-middle" rowspan=2> No. Surat Tugas </th>
      <th class="align-middle" rowspan=2> No. SPD </th>
      <th class="align-middle" rowspan=2> Kegiatan </th>
      <th class="align-middle" rowspan=2> Penyelenggara/Tujuan </th>
      <th class="align-middle" colspan=2> Lokasi </th>
      <th class="align-middle" colspan=2> Tanggal </th>
      <th class="align-middle" rowspan=2> Jmlh Hari </th>
      <th class="align-middle" colspan=8> Biaya Perjalanan Dinas </th>
      <th class="align-middle" colspan=7> Penginapan </th>
      <th class="align-middle" colspan=6> Pesawat Berangkat </th>
      <th class="align-middle" colspan=6> Pesawat Pulang </th>
    </tr>
    <tr> 
      <th class="align-middle"> Nama </th>
      <th class="align-middle"> Jabatan </th>
      <th class="align-middle"> Dari </th>
      <th class="align-middle"> Tujuan </th>
      <th class="align-middle"> Berangkat </th>
      <th class="align-middle"> Kembali </th>
      <th class="align-middle"> Uang Saku </th>
      <th class="align-middle"> Uang Makan </th>
      <th class="align-middle"> Uang Representasi </th>
      <th class="align-middle"> Uang Penginapan </th>
      <th class="align-middle"> Taksi/Travel </th>
      <th class="align-middle"> Tiket Pesawat </th>
      <th class="align-middle"> Lain-lain </th>
      <th class="align-middle"> Jumlah Dibayarkan </th>
      <th class="align-middle"> Hotel </th>
      <th class="align-middle"> Room </th>
      <th class="align-middle"> Tgl. Check In </th>
      <th class="align-middle"> Tgl. Check Out </th>
      <th class="align-middle"> Hari </th>
      <th class="align-middle"> Rate per Malam </th>
      <th class="align-middle"> Jumlah Dibayarkan </th>
      <th class="align-middle"> Maskapai </th>
      <th class="align-middle"> Nomor Tiket </th>
      <th class="align-middle"> Kode Booking </th>
      <th class="align-middle"> Nomor Penerbangan </th>
      <th class="align-middle"> Tanggal </th>
      <th class="align-middle"> Jumlah Dibayarkan </th>
      <th class="align-middle"> Maskapai </th>
      <th class="align-middle"> Nomor Tiket </th>
      <th class="align-middle"> Kode Booking </th>
      <th class="align-middle"> Nomor Penerbangan </th>
      <th class="align-middle"> Tanggal </th>
      <th class="align-middle"> Jumlah Dibayarkan </th>
    </tr>
    <tr>
    @for ($i = 1; $i <= 40; $i++)
      <th> ({{ $i }}) </th>
    @endfor
    </tr>
  </thead>
  <tbody>
    @foreach ($data as $key => $dt)
      <tr>
        <td>{{ $key + 1 }}</td>
        <td>{{ $dt->nama_pelaksana }}</td>
        <td>{{ $dt->jabatan }}</td>
        <td>{{ $dt->no_pku }}</td>
        <td>{{ $dt->no_spt }}</td>
        <td>{{ $dt->no_sppd }}</td>
        <td>{{ $dt->kegiatan }}</td>
        <td>{{ $dt->penyelenggara }}</td>
        <td>{{ $dt->lok_asal }}</td>
        <td>{{ $dt->lok_tujuan }}</td>
        <td>{{ $dt->tgl_berangkat }}</td>
        <td>{{ $dt->tgl_kembali }}</td>
        <td>-</td>
        <td>{{ $dt->uang_saku }}</td>
        <td>{{ $dt->uang_makan }}</td>
        <td>{{ $dt->uang_representasi }}</td>
        <td>{{ $dt->uang_penginapan }}</td>
        <td>{{ $dt->uang_travel }}</td>
        <td>{{ $dt->uang_pesawat }}</td>
        <td>-</td>
        <td>{{ $dt->uang_total }}</td>
        <td>{{ $dt->inap_hotel }}</td>
        <td>{{ $dt->inap_room }}</td>
        <td>{{ $dt->inap_checkin }}</td>
        <td>{{ $dt->inap_checkout }}</td>
        <td>{{ $dt->inap_jml_hari }}</td>
        <td>{{ $dt->inap_per_malam }}</td>
        <td>{{ $dt->inap_jumlah }}</td>
        <td>{{ $dt->pesbrgkt_maskapai }}</td>
        <td>{{ $dt->pesbrgkt_no_tiket }}</td>
        <td>{{ $dt->pesbrgkt_kode_booking }}</td>
        <td>{{ $dt->pesbrgkt_no_penerbangan }}</td>
        <td>-</td>
        <td>{{ $dt->peskmbl_maskapai }}</td>
        <td>{{ $dt->peskmbl_no_tiket }}</td>
        <td>{{ $dt->peskmbl_kode_booking }}</td>
        <td>{{ $dt->peskmbl_no_penerbangan }}</td>
        <td>-</td>
      </tr>
    @endforeach
  </tbody>
</table>
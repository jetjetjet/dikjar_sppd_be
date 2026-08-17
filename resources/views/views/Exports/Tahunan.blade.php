<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

  </head>
  <body>
    <table class="table table-bordered">
      <thead>
        <tr>
          <th rowspan=40> asdasdasda </th>
        </tr>
        <tr> 
          <th class="align-middle" rowspan=3> No. </th>
          <th class="align-middle" colspan=2> Pelaksana </th>
          <th class="align-middle" rowspan=3> No. BKU </th>
          <th class="align-middle" rowspan=3> No. Surat Tugas </th>
          <th class="align-middle" rowspan=3> No. SPD </th>
          <th class="align-middle" rowspan=3> Kegiatan </th>
          <th class="align-middle" rowspan=3> Penyelenggara/Tujuan </th>
          <th class="align-middle" colspan=2> Lokasi </th>
          <th class="align-middle" colspan=2> Tanggal </th>
          <th class="align-middle" rowspan=3> Jmlh Hari </th>
          <th class="align-middle" colspan=8> Biaya Perjalanan Dinas </th>
          <th class="align-middle" colspan=7> Penginapan </th>
          <th class="align-middle" colspan=6> Pesawat Berangkat </th>
          <th class="align-middle" colspan=6> Pesawat Pulang </th>
        </tr>
        <tr> 
          <th class="align-middle" rowspan=2> Nama </th>
          <th class="align-middle" rowspan=2> Jabatan </th>
          <th class="align-middle" rowspan=2> Dari </th>
          <th class="align-middle" rowspan=2> Tujuan </th>
          <th class="align-middle" rowspan=2> Berangkat </th>
          <th class="align-middle" rowspan=2> Kembali </th>
          <th class="align-middle" rowspan=2> Uang Saku </th>
          <th class="align-middle" rowspan=2> Uang Makan </th>
          <th class="align-middle" rowspan=2> Uang Representasi </th>
          <th class="align-middle" rowspan=2> Uang Penginapan </th>
          <th class="align-middle" rowspan=2> Taksi/Travel </th>
          <th class="align-middle" rowspan=2> Tiket Pesawat </th>
          <th class="align-middle" rowspan=2> Lain-lain </th>
          <th class="align-middle" rowspan=2> Jumlah Dibayarkan </th>
          <th class="align-middle" rowspan=2> Hotel </th>
          <th class="align-middle" rowspan=2> Room </th>
          <th class="align-middle" rowspan=2> Tgl. Check In </th>
          <th class="align-middle" rowspan=2> Tgl. Check Out </th>
          <th class="align-middle" rowspan=2> Hari </th>
          <th class="align-middle" rowspan=2> Rate per Malam </th>
          <th class="align-middle" rowspan=2> Jumlah Dibayarkan </th>
          <th class="align-middle" rowspan=2> Maskapai </th>
          <th class="align-middle" rowspan=2> Nomor Tiket </th>
          <th class="align-middle" rowspan=2> Kode Booking </th>
          <th class="align-middle" rowspan=2> Nomor Penerbangan </th>
          <th class="align-middle" rowspan=2> Tanggal </th>
          <th class="align-middle" rowspan=2> Jumlah Dibayarkan </th>
          <th class="align-middle" rowspan=2> Maskapai </th>
          <th class="align-middle" rowspan=2> Nomor Tiket </th>
          <th class="align-middle" rowspan=2> Kode Booking </th>
          <th class="align-middle" rowspan=2> Nomor Penerbangan </th>
          <th class="align-middle" rowspan=2> Tanggal </th>
          <th class="align-middle" rowspan=2> Jumlah Dibayarkan </th>
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

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
  </body>
</html>


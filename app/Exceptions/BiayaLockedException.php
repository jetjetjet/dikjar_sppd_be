<?php

namespace App\Exceptions;

/**
 * Dilempar saat mencoba tambah/ubah/hapus/upload struk biaya (transport/inap/
 * pengeluaran) pada SPT yang sudah di-void atau sudah settled. Beda dari
 * \Exception biasa supaya Controller bisa menampilkan pesannya apa adanya ke
 * user (bukan pesan generik "Kesalahan! Tidak dapat memproses.") — lihat
 * BiayaService::assertEditable().
 */
class BiayaLockedException extends \Exception {}

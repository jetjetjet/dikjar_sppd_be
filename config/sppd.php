<?php

return [
    /*
    |--------------------------------------------------------------------------
    | SPPD Application Configuration
    |--------------------------------------------------------------------------
    */

    // Public base URL used for SPT verification QR codes.
    'verification_url' => env('SPPD_VERIFICATION_URL', 'https://sppd.disdikkerinci.id/verifikasi'),

    // Default public (local) filesystem disk.
    'public_disk' => env('SPPD_PUBLIC_DISK', 'public'),

    // Cloud filesystem disk used when FILESYSTEM_DISK=s3/minio.
    'cloud_disk' => env('SPPD_CLOUD_DISK', 's3'),

    // Directory (relative to base_path()) where document Word templates live.
    // Sengaja di luar public/storage — path itu reserved untuk symlink `storage:link`
    // (lihat DEPLOYMENT.md Lampiran A untuk penjelasan lengkap).
    'template_dir' => env('SPPD_TEMPLATE_DIR', 'storage/app/templates'),

    // Template filenames used by DocumentGeneratorService, keyed by logical name.
    // Ganti nama file di sini kalau template diganti — tidak perlu ubah kode.
    'templates' => [
        'sppd' => 'template_sppd.docx',
        'rumming' => 'template_rumming.docx',
        'kwitansi' => 'template_kwitansi.docx',
        'laporan' => 'template_laporan.docx',
        'spt_bupati' => 'template_spt_bupati.docx', // pttd_id 2, 3
        'spt_sekda' => 'template_spt_sekda.docx',   // pttd_id 4
        // 'spt_default' => 'template_spt.docx',       // pttd_id 219
        'spt_default' => 'spt_default_rev.docx',       // pttd_id 219
        'spt_an' => 'template_spt_an.docx',         // pttd_id lainnya
        // Lapisan fallback terakhir kalau template pejabat (upload) MAUPUN template
        // legacy di atas dua-duanya gagal resolve (mis. file hilang dari disk) —
        // lihat DocumentGeneratorService::resolveLegacySptTemplate() dan
        // PLAN_TEMPLATE_PER_PEJABAT.md Keputusan #4.
        'spt_fallback_default' => 'template_spt_an.docx',
    ],
];

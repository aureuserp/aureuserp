<?php

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Webkul\Employee\Models\EmployeeResumeAttachment;

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../Helpers/EmployeeHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('employees');

    Storage::fake('public');
});

it('allows the document and image formats requested in the issue', function () {
    expect(EmployeeResumeAttachment::ACCEPTED_MIME_TYPES)
        ->toContain('application/pdf')
        ->toContain('application/msword')
        ->toContain('application/vnd.openxmlformats-officedocument.wordprocessingml.document')
        ->toContain('text/plain')
        ->toContain('image/png')
        ->toContain('image/jpeg')
        ->toContain('image/webp');
});

it('does not allow arbitrary archive or executable uploads', function () {
    expect(EmployeeResumeAttachment::ACCEPTED_MIME_TYPES)
        ->not->toContain('application/zip')
        ->not->toContain('application/x-msdownload');
});

it('caps uploads at ten megabytes', function () {
    expect(EmployeeResumeAttachment::MAX_UPLOAD_SIZE)->toBe(10240);
});

it('derives metadata for an uploaded certificate image', function () {
    $resume = EmployeeHelper::resume();

    $path = UploadedFile::fake()
        ->createWithContent('certificate.png', str_repeat('a', 4096))
        ->store(EmployeeResumeAttachment::UPLOAD_DIRECTORY, 'public');

    $attachment = $resume->attachments()->create([
        'file_path'          => $path,
        'original_file_name' => 'certificate.png',
    ]);

    expect($attachment->mime_type)->toBe('image/png')
        ->and((int) $attachment->file_size)->toBe(4096)
        ->and($attachment->file_path)->toStartWith(EmployeeResumeAttachment::UPLOAD_DIRECTORY);
});

it('keeps several attachments on one resume line', function () {
    $resume = EmployeeHelper::resume();

    foreach (['cv.pdf', 'degree.png', 'reference.txt'] as $name) {
        $resume->attachments()->create([
            'file_path'          => EmployeeResumeAttachment::UPLOAD_DIRECTORY.'/'.$name,
            'original_file_name' => $name,
        ]);
    }

    expect($resume->fresh()->attachments)->toHaveCount(3);
});

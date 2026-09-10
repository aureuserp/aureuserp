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

it('stores an attachment against a resume with its file metadata', function () {
    $resume = EmployeeHelper::resume();

    // createWithContent is deliberate. UploadedFile::fake()->create() only fakes the
    // reported size and writes an empty file, so the stored file would be 0 bytes and
    // the derived file_size could not be asserted meaningfully.
    $path = UploadedFile::fake()
        ->createWithContent('cv.pdf', str_repeat('a', 2048))
        ->store('employees/resumes', 'public');

    $attachment = $resume->attachments()->create([
        'name'               => 'Curriculum vitae',
        'file_path'          => $path,
        'original_file_name' => 'cv.pdf',
    ]);

    expect($attachment->employee_resume_id)->toBe($resume->id)
        ->and($attachment->mime_type)->toBe('application/pdf')
        ->and((int) $attachment->file_size)->toBeGreaterThan(0)
        ->and($resume->fresh()->attachments)->toHaveCount(1);
});

it('exposes a public url for the stored file', function () {
    $resume = EmployeeHelper::resume();

    $attachment = $resume->attachments()->create([
        'file_path'          => 'employees/resumes/cv.pdf',
        'original_file_name' => 'cv.pdf',
    ]);

    expect($attachment->url)->toContain('employees/resumes/cv.pdf');
});

it('removes the file from disk when the attachment is deleted', function () {
    $resume = EmployeeHelper::resume();

    $path = UploadedFile::fake()
        ->createWithContent('cv.pdf', str_repeat('a', 1024))
        ->store('employees/resumes', 'public');

    $attachment = $resume->attachments()->create([
        'file_path'          => $path,
        'original_file_name' => 'cv.pdf',
    ]);

    Storage::disk('public')->assertExists($path);

    $attachment->delete();

    Storage::disk('public')->assertMissing($path);
});

it('removes attachments and their files when the resume is deleted', function () {
    $resume = EmployeeHelper::resume();

    $path = UploadedFile::fake()
        ->createWithContent('certificate.png', str_repeat('a', 1024))
        ->store('employees/resumes', 'public');

    $resume->attachments()->create([
        'file_path'          => $path,
        'original_file_name' => 'certificate.png',
    ]);

    $resume->delete();

    Storage::disk('public')->assertMissing($path);
    expect(EmployeeResumeAttachment::query()->count())->toBe(0);
});

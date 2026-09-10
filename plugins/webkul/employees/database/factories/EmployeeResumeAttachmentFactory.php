<?php

namespace Webkul\Employee\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Employee\Models\EmployeeResume;
use Webkul\Employee\Models\EmployeeResumeAttachment;

/**
 * @extends Factory<EmployeeResumeAttachment>
 */
class EmployeeResumeAttachmentFactory extends Factory
{
    protected $model = EmployeeResumeAttachment::class;

    public function definition(): array
    {
        return [
            'employee_resume_id' => EmployeeResume::factory(),
            'name'               => fake()->sentence(3),
            'file_path'          => EmployeeResumeAttachment::UPLOAD_DIRECTORY.'/'.fake()->uuid().'.pdf',
            'original_file_name' => 'resume.pdf',
            'mime_type'          => 'application/pdf',
            'file_size'          => (string) fake()->numberBetween(1000, 500000),
        ];
    }
}

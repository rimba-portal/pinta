<?php

declare(strict_types=1);

namespace Rimba\Request\Listeners;

use Packstub\FormBuilder\Events\SubmissionReceived;
use Rimba\Request\Models\Offering;
use Rimba\Request\Models\Request;

class CreateRequestFromFormSubmission
{
    public function handle(SubmissionReceived $event): void
    {
        $offering = Offering::query()
            ->where('form_id', $event->form->getKey())
            ->first();

        /*
         * The form may belong to Form Builder without being
         * associated with a Rimba Offering.
         */
        if (! $offering) {
            return;
        }

        /*
         * Prevent duplicate Request creation.
         */
        if (
            Request::query()
                ->where(
                    'form_submission_id',
                    $event->submission->getKey(),
                )
                ->exists()
        ) {
            return;
        }

        Request::create([
            'number' => $this->nextRequestNumber(),

            'offering_id' => $offering->getKey(),

            'phase_code' => config(
                'request.phases.submitted',
                'oir.01',
            ),

            'form_submission_id' => $event->submission->getKey(),

            'submitted_at' => now(),
        ]);
    }

    protected function nextRequestNumber(): string
    {
        return 'REQ-'.now()->format('YmdHisv');
    }
}

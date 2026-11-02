<?php

declare(strict_types=1);

namespace Rimba\Request\Listeners;

use Packstub\FormBuilder\Events\SubmissionReceived;
use Rimba\Request\Models\Offering;
use Rimba\Request\Models\Request;

final class CreateRequestFromFormSubmission
{
    public function handle(SubmissionReceived $event): void
    {
        $offering = Offering::query()
            ->where('form_id', $event->form->getKey())
            ->first();

        /*
         * The submitted form may be a general Packstub form that is not
         * associated with a Rimba Offering.
         */
        if ($offering === null) {
            return;
        }

        Request::query()->firstOrCreate(
            [
                'form_submission_id' => $event->submission->getKey(),
            ],
            [
                'number' => $this->nextRequestNumber(),
                'offering_id' => $offering->getKey(),
                'requested_by' => $this->requestedBy(),
                'org_unit_id' => $offering->org_unit_id,
                'org_team_id' => $offering->org_team_id,
                'phase_code' => config(
                    'bites.request.phases.submitted',
                    'oir.01',
                ),
                'submitted_at' => now(),
            ],
        );
    }

    private function requestedBy(): int|string|null
    {
        /*
         * Replace this mapping later if Packstub's submission user_id
         * points to User rather than Staff.
         */
        return null;
    }

    private function nextRequestNumber(): string
    {
        return sprintf(
            'REQ-%s-%s',
            now()->format('YmdHis'),
            str()->upper(str()->random(6)),
        );
    }
}

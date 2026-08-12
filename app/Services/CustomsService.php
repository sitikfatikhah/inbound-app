<?php

namespace App\Services;

use App\Contracts\ActivityLoggerInterface;
use App\Contracts\CustomsServiceInterface;
use App\Models\CustomsClearances;

class CustomsService implements CustomsServiceInterface
{
    public function __construct(
        protected ActivityLoggerInterface $activity
    ) {}

    public function create(array $data): CustomsClearances
    {
        $customs = CustomsClearances::create($data);

        $this->activity->log(
            module: 'CustomsClearance',
            action: 'CREATE',
            model: $customs,
            new: $customs->toArray(),
            description: 'Customs clearance berhasil dibuat',
        );

        return $customs;
    }

    public function update(CustomsClearances $customs, array $data): CustomsClearances
    {
        $old = $customs->getOriginal();

        $customs->update($data);

        $this->activity->log(
            module: 'CustomsClearance',
            action: 'UPDATE',
            model: $customs,
            old: $old,
            new: $customs->fresh()->toArray(),
            description: 'Customs clearance diperbarui'
        );

        return $customs->fresh();
    }

    public function delete(CustomsClearances $customs): bool
    {
        $old = $customs->toArray();

        $this->activity->log(
            module: 'CustomsClearance',
            action: 'DELETE',
            model: $customs,
            old: $old,
            description: 'Customs clearance dihapus'
        );
        return $customs->delete();
    }
}
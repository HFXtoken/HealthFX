<?php

namespace Modules\Facility\Repositories\Eloquent;

use App\Http\Helpers\ApplicationHelper;
use Modules\Core\Repositories\Eloquent\EloquentBaseRepository;
use Modules\Facility\Events\BranchWasCreated;
use Modules\Facility\Events\BranchWasDeleted;
use Modules\Facility\Events\BranchWasUpdated;
use Modules\Facility\Repositories\BranchRepository;

class EloquentBranchRepository extends EloquentBaseRepository implements BranchRepository
{
    public function create($data)
    {
        if (array_get($data, 'is_primary') === '1') {
            $this->removeOtherPrimaryBranch(array_get($data, 'facility_id'));
        }

        $branch = $this->model->create($data);

        if(isset($data['contact']) && array_filter($data['contact'])) {
            $branch->contact()->create($data['contact']);
        }

        if(isset($data['specialities']) && array_filter($data['specialities'])) {
            $branch->specialities()->sync($data['specialities']);
        }

        if(isset($data['spokenLanguages']) && array_filter($data['spokenLanguages'])) {
            $branch->spokenLanguages()->sync($data['spokenLanguages']);
        }

        if(isset($data['timings']) && array_filter($data['timings'])) {
            $branch->timings()->createMany($data['timings']);
        }

        if(isset($data['location']) && array_filter($data['location'])) {
            $branch->location()->create($data['location']);
        }

        if(isset($data['doctor']) && array_filter($data['doctor'])) {
            $data['doctor'] = ApplicationHelper::removeKeysFromArray($data['doctor'], ['doctor_name', 'doctor_speciality']);
            $branch->doctors()->sync($data['doctor']);
        }

        $branch->setTags(array_get($data, 'tags', []));
        event(new BranchWasCreated($branch, $data));

        return $branch;
    }

    public function update($branch, $data)
    {
        if (array_get($data, 'is_primary') === '1') {
            $this->removeOtherPrimaryBranch($branch->facility_id, $branch->id);
        }

        $branch->update($data);

        if(isset($data['contact']) && array_filter($data['contact'])) {
            $branch->contact()->update($data['contact']);
        } else {
            $branch->contact()->delete();
        }

        if(isset($data['specialities']) && array_filter($data['specialities'])) {
            $branch->specialities()->sync($data['specialities']);
        } else {
            $branch->specialities()->detach();
        }

        if(isset($data['spokenLanguages']) && array_filter($data['spokenLanguages'])) {
            $branch->spokenLanguages()->sync($data['spokenLanguages']);
        } else {
            $branch->spokenLanguages()->detach();
        }

        if(isset($data['timings']) && array_filter($data['timings'])) {
            foreach ($data['timings'] as $timing) {
                $branch->timings()->where('day_of_week', $timing['day_of_week'])->update($timing);
            }
        } else {
            $branch->timings()->delete();
        }

        if(isset($data['location']) && array_filter($data['location'])) {
            $branch->location()->update($data['location']);
        } else {
            $branch->location()->delete();
        }

        if(isset($data['doctor']) && array_filter($data['doctor'])) {
            $data['doctor'] = ApplicationHelper::removeKeysFromArray($data['doctor'], ['doctor_name', 'doctor_speciality']);
            foreach ($branch->doctors as $doctor) {
                if (!array_key_exists($doctor->id, $data['doctor'])) {
                    $doctor->pivot->timings()->delete();
                }
            }
            $branch->doctors()->sync($data['doctor']);
        } else {
            foreach ($branch->doctors as $doctor) {
                $doctor->pivot->timings()->delete();
            }
            $branch->doctors()->detach();
        }

        $branch->setTags(array_get($data, 'tags', []));
        event(new BranchWasUpdated($branch, $data));

        return $branch;
    }

    public function destroy($branch)
    {
        $branch->location()->delete();
        $branch->timings()->delete();
        $branch->contact()->delete();
        $branch->spokenLanguages()->detach();
        $branch->specialities()->detach();
        $branch->packages()->detach();
        foreach ($branch->doctors as $doctor) {
            $doctor->pivot->timings()->delete();
        }
        $branch->doctors()->detach();

        $branch->untag();
        event(new BranchWasDeleted($branch));

        return $branch->delete();
    }

    public function findByName($keyword)
    {
        return $this->model->whereTranslationILike('name', '%'.$keyword.'%')->get();
    }

    public function findPrimaryBranch($facility_id)
    {
        return $this->model->where('facility_id', $facility_id)
            ->where('is_primary', 1)->first();
    }

    private function removeOtherPrimaryBranch($facility_id, $entityId = null)
    {
        $primaryBranch = $this->findPrimaryBranch($facility_id);
        if ($primaryBranch === null) {
            return;
        }
        if ($entityId === $primaryBranch->id) {
            return;
        }

        $primaryBranch->is_primary = 0;
        $primaryBranch->save();
    }
}

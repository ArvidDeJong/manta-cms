<?php

namespace Manta\Traits;

trait MantaTrait
{
    public ?int $trashed = null;
    public ?string $deleteId = null;
    public ?string $deleteClass = null;
    public ?string $showTab = null;
    public array $locale_info = [];
    public array $config = [];
    public string $breadcumbHomeName = 'Dashboard';
    public array $fields = [];

    public function delete($class, $id)
    {
        $this->deleteClass = $class;
        $this->deleteId = $id;
    }

    public function deleteCancel()
    {
        $this->deleteId = null;
    }

    public function deleteConfirm()
    {
        $this->deleteClass::where('id', $this->deleteId)->update(['deleted_by' => auth('staff')->user()->name]);
        $this->deleteClass::find($this->deleteId)->delete();
        $this->deleteId = null;
        $this->trashed = count($this->deleteClass::onlyTrashed()->get());
    }

    public function restore($class, $id)
    {
        $class::withTrashed()->where('id', $id)->restore();
        $this->trashed = count($class::onlyTrashed()->get());
        $this->showTab = 'active';
    }

    public function getLocaleInfo()
    {
        // Stel de standaard locale in als $this->locale niet gezet is
        $this->locale = $this->locale ?? config('manta.locale');

        // Probeer de lokale informatie te halen en gebruik de standaard als fallback
        $this->locale_info = collect(config('manta.locales'))
            ->firstWhere('locale', $this->locale)
            ?? collect(config('manta.locales'))
            ->firstWhere('locale', config('manta.locale'));

        // Update $this->locale met de mogelijk bijgewerkte informatie
        $this->locale = $this->locale_info['locale'];
    }
}

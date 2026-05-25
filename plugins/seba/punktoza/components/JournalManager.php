<?php namespace Seba\Punktoza\Components;

use Cms\Classes\ComponentBase;
use Request;
use Seba\Punktoza\Models\Journal;

class JournalManager extends ComponentBase
{
    public $journals;
    public $messageSuccess;
    public $messageError;
    
    public $searchPhrase;
    public $searchDiscipline;
    public $selectedPoints;

    public function componentDetails()
    {
        return [
            'name'        => 'Menedżer Czasopism',
            'description' => 'Obsługuje import, zaawansowane filtrowanie i stronicowanie czasopism'
        ];
    }

    public function onRun()
    {
        if (Request::isMethod('post') && Request::hasFile('csv_file')) {
            $file = Request::file('csv_file');
            if ($file->isValid()) {
                $this->processCsv($file->getRealPath());
                $this->messageSuccess = 'Wykaz został pomyślnie zaimportowany!';
            } else {
                $this->messageError = 'Błąd pliku. Plik jest za duży.';
            }
        }

        $this->searchPhrase = Request::input('search_phrase');
        $this->searchDiscipline = Request::input('search_discipline');
        $this->selectedPoints = Request::input('selected_points');

        $query = Journal::query();

        if (!empty($this->searchPhrase)) {
            $query->where(function($q) {
                $q->where('title', 'like', '%' . $this->searchPhrase . '%')
                  ->orWhere('issn', 'like', '%' . $this->searchPhrase . '%')
                  ->orWhere('eissn', 'like', '%' . $this->searchPhrase . '%')
                  ->orWhere('uid', 'like', '%' . $this->searchPhrase . '%');
            });
        }

        if (!empty($this->searchDiscipline)) {
            $query->where('disciplines', 'like', '%' . $this->searchDiscipline . '%');
        }

        if (!empty($this->selectedPoints)) {
            $query->where('points', (int)$this->selectedPoints);
        }

        $this->journals = $query->paginate(50)->appends(Request::query());
    }

    private function processCsv($filePath)
    {
        set_time_limit(300);
        if (($handle = fopen($filePath, "r")) !== FALSE) {
            $disciplinesHeader = fgetcsv($handle, 0, ";");
            $columnsHeader = fgetcsv($handle, 0, ";");

            Journal::truncate();

            \Db::transaction(function () use ($handle, $disciplinesHeader) {
                while (($data = fgetcsv($handle, 0, ";")) !== FALSE) {
                    if (empty($data[1]) || !is_numeric($data[1])) {
                        continue;
                    }

                    $assignedDisciplines = [];
                    for ($i = 9; $i < count($data); $i++) {
                        if (isset($data[$i]) && strtolower(trim($data[$i])) === 'x') {
                            if (!empty($disciplinesHeader[$i])) {
                                $assignedDisciplines[] = trim($disciplinesHeader[$i]);
                            }
                        }
                    }

                    Journal::create([
                        'uid'         => $data[1],
                        'title'       => $data[2] ?: null,
                        'issn'        => $data[3] ?: null,
                        'eissn'       => $data[4] ?: null,
                        'points'      => isset($data[8]) ? (int)$data[8] : 0,
                        'disciplines' => $assignedDisciplines,
                    ]);
                }
            });
            fclose($handle);
        }
    }
}
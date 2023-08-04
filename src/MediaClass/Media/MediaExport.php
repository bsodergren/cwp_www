<?php
/**
 * CWP Media tool
 */

namespace CWP\Media;

use CWP\Utils\Zip;
use Nette\Utils\FileSystem;

class MediaExport
{
    public $mediaArray  = [];
    public $formNumbers = [];

    public function __construct($media)
    {
        $this->media      = $media;
        $this->mediaArray = $media->MediaArray;
    }

    private function getFirstKey()
    {
        return array_key_first($this->mediaArray);
    }

    public function getPDF()
    {
        return $this->media->pdf_fullname;
    }

    public function getJobNumber()
    {
        return $this->mediaArray[$this->getFirstKey()]['job_number'];
    }

    public function getFormList()
    {
        foreach ($this->mediaArray as $number => $data) {
            $formNumbers[] = $number;
        }

        return $formNumbers;
    }

    public function getCount($form_number)
    {
        return $this->mediaArray[$form_number]['count'];
    }

    public function getProduct($form_number)
    {
        return $this->mediaArray[$form_number]['product'];
    }

    public function getFormConfig($form_number)
    {
        return $this->mediaArray[$form_number]['config'];
    }

    public function getFormBind($form_number)
    {
        return $this->mediaArray[$form_number]['bind'];
    }

    private function cleanFormData($formNumber)
    {
        $data['Front'] =    $this->mediaArray[$formNumber]['Front'];
        if (key_exists('Back', $this->mediaArray[$formNumber])) {
            $data['Back'] =    $this->mediaArray[$formNumber]['Back'];
        }

        foreach ($data as $former => $letterArray) {
            foreach ($letterArray as $letter => $parts) {
                foreach ($parts as $r => $rows) {
                    foreach ($rows as $key => $value) {
                        if ('form_id' == $key) {
                            continue;
                        }
                        if ('job_id' == $key) {
                            continue;
                        }
                        if ('job_number' == $key) {
                            continue;
                        }
                        $formData[$former][$letter][$r][$key] = $value;
                    }
                }
            }
        }

        return $formData;
    }

    public function getFormData($formNumber)
    {
        return $this->cleanFormData($formNumber);
    }

    public function exportJsonData()
    {
        $this->json_file      = __TEMP_DIR__ . DIRECTORY_SEPARATOR . basename($this->getPDF(), '.pdf'). ".json";

        $forms          = $this->getFormList();
        $formData       = [
            'pdf_file'   => $this->getPDF(),
            'product' => $this->mediaArray[$this->getFirstKey()]['product'],
            'job_number' => $this->getJobNumber(),
            'forms'      => [],
        ];

        foreach ($forms as $formNumber) {
            $formData['forms'][$formNumber] = [
                'product' => $this->getProduct($formNumber),
                'count'   => $this->getCount($formNumber),
                'config'  => $this->getFormConfig($formNumber),
                'bind'    => $this->getFormBind($formNumber),
                'form_number' => $formNumber,
                'form'    => $this->getFormData($formNumber),
            ];
        }

        $jsonString = json_encode($formData);
        FileSystem::write($this->json_file, $jsonString);

    }


    public function exportZip()
    {
        $this->exportJsonData();
        $zip            = new Zip();
        $zip->exportZip($this->getPDF(), $this->json_file );
    }
}

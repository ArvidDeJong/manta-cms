<?php

namespace Manta\Services;

class Openai
{
    public string $sourceLanguage = 'nl';
    public string $destinationLanguage = 'en';
    public ?string $question = null;

    public function call_api($fields)
    {
        $curl = curl_init();
        // Vervang in call_api method de authorization header als volgt:
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.openai.com/v1/chat/completions',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($fields),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: ' . env('OPENAI_KEY')
            ),
        ));

        curl_close($curl);

        return json_decode(curl_exec($curl), true);
        // dd($response);
    }

    public function translate()
    {
        if ($this->question == null) {
            return ['error' => 'No question entered'];
        }
        $fields = ["model" => "gpt-4", "temperature" => 0, "max_tokens" => 256];
        $fields['messages'] = [
            ["role" => "system", "content" => "You will be provided with a sentence in ISO language {$this->sourceLanguage}, and your task is to translate it into ISO language {$this->destinationLanguage}."],
            ["role" => "user", "content" => $this->question]
        ];
        $response = $this->call_api($fields);
        return ['answer' => $response['choices'][0]['message']['content']];
    }

    public function getSeoTitle()
    {
        if ($this->question == null) {
            return ['error' => 'No question entered'];
        }
        $fields = ["model" => "gpt-4", "temperature" => 0, "max_tokens" => 256];
        $fields['messages'] = [
            ["role" => "system", "content" => "Je krijgt een zin in ISO taal {$this->sourceLanguage} van 'ID oiltools uit Nederland', en je taak is om een SEO title te maken"],
            ["role" => "user", "content" => $this->question]
        ];
        $response = $this->call_api($fields);
        return ['answer' => $response['choices'][0]['message']['content']];
    }

    public function getSeoDescription()
    {
        if ($this->question == null) {
            return ['error' => 'No question entered'];
        }
        $fields = ["model" => "gpt-4", "temperature" => 0, "max_tokens" => 256];
        $fields['messages'] = [
            ["role" => "system", "content" => "Je krijgt een zin in ISO taal {$this->sourceLanguage} van 'ID oiltools uit Nederland', en je taak is om een SEO description te maken"],
            ["role" => "user", "content" => $this->question]
        ];
        $response = $this->call_api($fields);
        return ['answer' => $response['choices'][0]['message']['content']];
    }
}

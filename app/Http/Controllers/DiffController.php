<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;

class DiffController extends Controller
{

    public function form()
    {
        return view('form');
    }

    public function execute()
    {
        $data = request()->except('_token');
        $validator = Validator::make($data, [
            'original' => 'required',
            'corrected' => 'required',
        ], [
            'original.required' => 'Поле исходный текст не заполнено',
            'corrected.required' => 'Поле исправленный текст не заполнено',
        ]);

        if ($errors = $validator->errors()->all()) {
            return redirect()->back()->withErrors($errors)->withInput();
        }
        Session::put('diff', $this->diff($data['original'], $data['corrected']));

        return redirect()->route('diff.show');
    }

    public function show()
    {
        return view('show', ['diff' => Session::get('diff')]);
    }

    private function diff($original, $corrected)
    {
        $result = [
            'unchanged' => [],
            'changed' => [],
            'deleted' => [],
            'added' => [],
            'count' => 1,
            'original' => [$original],
            'corrected' => [$corrected]
        ];
        if ($original == $corrected) {
            $result['unchanged'] = [0];

            return $result;
        }

        $by_len = function ($v1, $v2) {
            return strlen($v1) > strlen($v2) ? -1 : 1;
        };

        $original = preg_replace('/\r/', '', $original);
        $original = explode(PHP_EOL, $original);
        $result['original'] = $original;

        $corrected = preg_replace('/\r/', '', $corrected);
        $corrected = explode(PHP_EOL, $corrected);

        $result['corrected'] = $corrected;

        $result['count'] = max(count($corrected), count($original));
        $used_i = [];
        $used_j = [];

        for ($i = 0; $i < count($original); $i++) {
            for ($j = 0; $j < count($corrected); $j++) {
                if (!in_array($j, $used_j)) {
                    if (strcmp($original[$i], $corrected[$j]) == 0) {
                        $result['unchanged'][$i] = $j;
                        $used_i[] = $i;
                        $used_j[] = $j;
                        break;
                    }
                }
            }
        }

        for ($i = 0; $i < count($original); $i++) {
            if (!in_array($i, $used_i)) {
                $tmp_l = 0;
                for ($j = 0; $j < count($corrected); $j++) {
                    if (!in_array($j, $used_j)) {
                        if (($l = $this->lcs_length($original[$i], $corrected[$j])) > $tmp_l) {
                            $tmp_l = $l;
                            $result['changed'][$i] = $j;
                        }
                    }
                }
                if (isset($result['changed'][$i])) {
                    $used_j[] = $result['changed'][$i];
                    $used_i[] = $i;
                }
            }
        }

        $result['deleted'] = array_diff(array_keys($original), array_keys($result['changed'] + $result['unchanged']));
        $result['added'] = array_diff(array_keys($corrected), $result['changed'] + $result['unchanged']);
        $result['changed'] = array_flip($result['changed']);

        return $result;
    }

    private function lcs_length($str1, $str2)
    {
        $pattern = '/i+|I+|a+|Ä+|ä+|å+|Å+|A+|e+|E+|o+|O+|y+|Y+|u+|U+|ö+|Ö+|\.+|\!+|,+|\s+|[0-9]+/';
        $str1 = preg_replace($pattern, '', $str1);
        $str2 = preg_replace($pattern, '', $str2);
        $strlen1 = strlen($str1);
        $strlen2 = strlen($str2);

        if ($strlen1 == 0 || $strlen2 == 0) {
            return 0;
        }
        $matrix = array_fill(0, $strlen1 + 1, array_fill(0, $strlen2 + 1, 0));

        for ($i = 1; $i <= $strlen1; $i++) {
            for ($j = 1; $j <= $strlen2; $j++) {
                if ($str1[$i - 1] == $str2[$j - 1]) {
                    $matrix[$i][$j] = max($matrix[$i][$j - 1], $matrix[$i - 1][$j]) + 1;
                } else {
                    $matrix[$i][$j] = max($matrix[$i][$j - 1], $matrix[$i - 1][$j]);
                }
            }
        }

        return $matrix[$strlen1][$strlen2] / max($strlen2, $strlen1);
    }


}

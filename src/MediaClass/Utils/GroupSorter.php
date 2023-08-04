<?php
/**
 * CWP Media tool
 */

namespace CWP\Utils;

class GroupSorter
{
    public $bestGroupSize = 2;

    public function __construct($bestGroupSize)
    {
        $this->bestGroupSize = $bestGroupSize;
    }

    public function sortIntoGroups($numbers)
    {
        $result = [];
        arsort($numbers); // Sort the numbers in descending order based on values

        while (!empty($numbers)) {
            $group = [];

            // Take the largest remaining number and add it to the group
            $group[] = key($numbers);
            unset($numbers[key($numbers)]);

            $remainingCount = count($numbers);

            // Find the best group size (2, 3, or 4) that minimizes the difference in group sums

            $bestGroupDiff = \PHP_INT_MAX;

            for ($groupSize = 2; $groupSize <= min($remainingCount, 4); ++$groupSize) {
                // Calculate the sum of the current group
                $groupSum = array_sum($group);

                // Find the remaining number(s) that minimize the difference in group sums
                $combinations = $this->generateCombinations(array_keys($numbers), $groupSize - 1);

                foreach ($combinations as $combination) {
                    $combinationSum = array_sum($combination);
                    $diff = abs($groupSum - $combinationSum);

                    if ($diff < $bestGroupDiff) {
                        $bestGroupDiff = $diff;
                        $this->bestGroupSize = $groupSize;
                    }
                }
            }

            // Add the remaining numbers that minimize the difference in group sums to the group
            $combination = array_shift($this->generateCombinations(array_keys($numbers), $this->bestGroupSize - 1));
            foreach ($combination as $num) {
                $group[] = $num;
                unset($numbers[$num]);
            }

            $result[] = $group;
        }

        return $result;
    }

    private function generateCombinations($numbers, $length)
    {
        $result = [];

        $totalCombinations = pow(2, count($numbers));

        for ($i = 1; $i < $totalCombinations; ++$i) {
            $binary = str_pad(decbin($i), count($numbers), '0', \STR_PAD_LEFT);

            if (substr_count($binary, '1') == $length) {
                $combination = [];

                for ($j = 0; $j < count($numbers); ++$j) {
                    if ('1' == $binary[$j]) {
                        $combination[] = $numbers[$j];
                    }
                }

                $result[] = $combination;
            }
        }

        return $result;
    }
}

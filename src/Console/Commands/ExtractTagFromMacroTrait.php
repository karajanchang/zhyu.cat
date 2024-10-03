<?php

namespace Cat\Console\Commands;

trait ExtractTagFromMacroTrait
{
    private function extractTag($classString) {
        // First, remove the '::class' part
        $classString = str_replace('::class', '', $classString);

        // Then, split by backslash and extract the part between 'Cat\Helpers\' and the class name
        $parts = explode('\\', $classString);

        if (count($parts) >= 4) {
            return $parts[2]; // Return the third part, which is the tag (e.g., 'Network' or 'general')
        }

        return null; // If not a valid class string, return null
    }
}
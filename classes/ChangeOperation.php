<?php
namespace Utopigs\Pigsync\Classes;

interface ChangeOperation
{
    public function processChanges(SourceControl $source_control);
}

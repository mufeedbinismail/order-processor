<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MetaReference extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * Calculates the next reference no
     * 
     * @param int $type
     * @param array $context
     * @param bool $persist Determines if the reference should be persisted.
     * @return int
     * 
     * @throws \InvalidArgumentException
     */
    public static function getNext($type, $context = null, $persist = false) {
        $definition = ReferencePattern::where('system_type_id', $type)->first();
        
        if (!$definition) {
            throw new \InvalidArgumentException("Reference pattern not found for type: {$type}");
        }
        
        [$template, $prefix, $numericPlaceHolder, $postfix] = ReferencePattern::parsePattern($definition->pattern, $context);

        if ($persist == true) {
            // If we should persist then we must be inside a transaction to ensure the reference is unique
            if (!\DB::transactionLevel()) {
                throw new \InvalidArgumentException("Cannot persist reference outside a transaction");
            }

            // If the next reference is being fetched inside a transaction, Lets ensure nobody can have access to it
            // update and get the number of affected rows
            $affectedRows = MetaReference::where('system_type_id', $type)
                ->where('template', $template)
                ->update(['next_seq' => \DB::raw('next_seq + 1')]);

            // If the affected rows is zero it means this is the first time we are encountering this pattern
            // So insert lazily
            if ($affectedRows === 0) {
                MetaReference::create([
                    'system_type_id' => $type,
                    'template' => $template,
                    'next_seq_no' => 2
                ]);
            }
        }

        // Retrieve the next reference number
        $nextSeq = MetaReference::where('system_type_id', $type)->where('template', $template)->value('next_seq_no') ?: 0;

        // if already persisted, decrement to get the correct reference
        $persist AND --$nextSeq;

        // Enable getting the next reference number for display purposes
        // This will allow calls to be made outside transaction to check the status of next
        // reference number. In that case sometimes the value may not yet be initialized
        // So initialize it to 1
        if ($nextSeq < 1) {
            MetaReference::updateOrCreate(
                [
                    'system_type_id' => $type,
                    'template' => $template
                ],
                [
                    'next_seq_no' => 1
                ]
            );
            $nextSeq = 1;
        }

        // pad 0 to the left to make it the correct length
        $nextSeq = str_pad($nextSeq, ReferencePattern::getDigitsFromSequence($numericPlaceHolder), "0", STR_PAD_LEFT);
        
        return $prefix . $nextSeq . $postfix;
    }
}

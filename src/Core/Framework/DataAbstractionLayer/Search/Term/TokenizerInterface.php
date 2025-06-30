<?php declare(strict_types=1);

namespace HeyPanel\Core\Framework\DataAbstractionLayer\Search\Term;

interface TokenizerInterface
{
    /**
     * @return list<string>
     */
    public function tokenize(string $string): array;
}

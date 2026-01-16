<?php

namespace App\Services;

class ArabicTextNormalizer
{
    /**
     * Normalize Arabic text for comparison
     * Handles common variations in Arabic writing
     */
    public static function normalize(string $text): string
    {
        $text = trim($text);

        // Remove Arabic diacritics (tashkeel): فتحة، ضمة، كسرة، سكون، شدة، تنوين
        $text = self::removeDiacritics($text);

        // Normalize Alef variations (أ إ آ ا) → ا
        $text = self::normalizeAlef($text);

        // Normalize Taa Marbouta (ة) ↔ Haa (ه) at end of words
        $text = self::normalizeTaaMarbouta($text);

        // Normalize Yaa variations (ى ي) → ي
        $text = self::normalizeYaa($text);

        // Normalize Waw with hamza (ؤ) → و
        $text = self::normalizeWaw($text);

        // Remove definite article (ال) for more flexible matching
        $text = self::removeDefiniteArticle($text);

        // Normalize spaces (multiple spaces → single space)
        $text = preg_replace('/\s+/u', ' ', $text);

        // Convert to lowercase for any mixed content
        $text = mb_strtolower($text);

        return trim($text);
    }

    /**
     * Remove Arabic diacritics (tashkeel)
     */
    private static function removeDiacritics(string $text): string
    {
        // Arabic diacritics Unicode range: 0x064B - 0x0652
        $diacritics = [
            "\u{064B}", // Fathatan
            "\u{064C}", // Dammatan
            "\u{064D}", // Kasratan
            "\u{064E}", // Fatha
            "\u{064F}", // Damma
            "\u{0650}", // Kasra
            "\u{0651}", // Shadda
            "\u{0652}", // Sukun
            "\u{0653}", // Maddah above
            "\u{0654}", // Hamza above
            "\u{0655}", // Hamza below
            "\u{0670}", // Superscript Alef
        ];

        return str_replace($diacritics, '', $text);
    }

    /**
     * Normalize Alef variations
     * أ إ آ → ا
     */
    private static function normalizeAlef(string $text): string
    {
        $alefVariations = [
            'أ', // Alef with Hamza above
            'إ', // Alef with Hamza below
            'آ', // Alef with Madda above
            'ٱ', // Alef Wasla
        ];

        return str_replace($alefVariations, 'ا', $text);
    }

    /**
     * Normalize Taa Marbouta (ة) to Haa (ه) at end of words
     * This handles cases like المدينة vs المدينه
     */
    private static function normalizeTaaMarbouta(string $text): string
    {
        // Replace ة at end of words with ه
        // Using word boundary that works with Arabic
        return preg_replace('/ة(?=\s|$)/u', 'ه', $text);
    }

    /**
     * Normalize Yaa variations
     * ى (Alef Maksura) → ي
     */
    private static function normalizeYaa(string $text): string
    {
        return str_replace('ى', 'ي', $text);
    }

    /**
     * Normalize Waw with hamza
     * ؤ → و
     */
    private static function normalizeWaw(string $text): string
    {
        return str_replace('ؤ', 'و', $text);
    }

    /**
     * Remove definite article (ال) from the beginning of words
     * This helps match "الرياض" with "رياض"
     */
    private static function removeDefiniteArticle(string $text): string
    {
        // Remove ال from the start of the string or after a space
        // Also handles ال followed by sun letters (assimilation)
        return preg_replace('/(?:^|\s)ال/u', ' ', $text);
    }

    /**
     * Compare two Arabic strings with normalization
     */
    public static function compare(string $str1, string $str2): bool
    {
        return self::normalize($str1) === self::normalize($str2);
    }

    /**
     * Calculate similarity between two Arabic strings (0-100)
     */
    public static function similarity(string $str1, string $str2): float
    {
        $normalized1 = self::normalize($str1);
        $normalized2 = self::normalize($str2);

        if ($normalized1 === $normalized2) {
            return 100.0;
        }

        similar_text($normalized1, $normalized2, $percent);

        return $percent;
    }
}

<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace qbank_genai\handler;

use stdClass;

/**
 * Base Class FileHandler for text extraction.
 *
 * @package    qbank_genai
 * @copyright  2023 Christian Grévisse <christian.grevisse@uni.lu>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class FileHandler {

    /**
     * Abstract method for extracting text from a file.
     * 
     * @param stdClass The file
     * @return string The extracted text
     */
    abstract public function extract_text(stdClass $file): string;
}

/**
 * Text extraction handler for PDF files which uses the smalot/pdfparser.
 */
class PDFHandler extends FileHandler {

    public function extract_text(stdClass $file): string {
        raise_memory_limit(MEMORY_HUGE);

        $parser = new \Smalot\PdfParser\Parser();

        $pdf = $parser->parseFile($file->path);
        $text = $pdf->getText();

        return $text;
    }

}

/**
 * Registry for file handlers.
 */
class HandlerRegistry {

    /**
     * Singleton instance
     */
    private static $instance = null;

    /**
     * Registered handlers
     */
    private static $handlers = null;

    /**
     * This private constructor initializes the list of registered handlers.
     */
    private function __construct() {
        self::$handlers = [
            "pdf" => new PDFHandler(),
        ];
    }

    /**
     * Singleton instance getter.
     * 
     * @return HandlerRegistry The singleton registry instance
     */
    public static function get_registry() {
        if (self::$instance == null) {
            self::$instance = new HandlerRegistry();
        }

        return self::$instance;
    }

    /**
     * Returns the file types for which a file handler is registered.
     * 
     * @return string[] The file types 
     */
    public function get_supported_types() {
        return array_keys(self::$handlers);
    }
    
    /**
     * Returns the handler registered for the file type (if any)
     * 
     * @param string $type The file type
     * @return FileHandler The corresponding handler or null
     */
    public function get_handler($type) {
        $type = strtolower($type);

        if (array_key_exists($type, self::$handlers)) {
            return self::$handlers[$type];
        } else {
            return null;
        }
    }

}

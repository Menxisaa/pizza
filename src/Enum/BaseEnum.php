<?php

namespace App\Enum;

enum BaseEnum: string
{
    // Bases
    case CLASSIC = 'classic';
    case THIN = 'thin';
    case THREE_LAYERS = 'three_layers';

    // Bordes rellenos
    case STUFFED_MOZZARELLA_CHILI = 'stuffed_mozzarella_chili';
    case STUFFED_CHEDDAR = 'stuffed_cheddar';
    case STUFFED_MOZZARELLA_TOMATO = 'stuffed_mozzarella_tomato';

    // Método para obtener opciones válidas (ej. validación)
    public static function validValues(): array
    {
        return array_column(self::cases(), 'value');
    }

    // Descripción legible para humanos
    public function label(): string
    {
        return match ($this) {
            self::CLASSIC => 'Base Clásica',
            self::THIN => 'Base Fina',
            self::THREE_LAYERS => 'Base 3 Pisos',
            self::STUFFED_MOZZARELLA_CHILI => 'Borde relleno de Mozzarella con Chili',
            self::STUFFED_CHEDDAR => 'Borde relleno de Fundido con Cheddar',
            self::STUFFED_MOZZARELLA_TOMATO => 'Borde relleno de Mozzarella con Tomate',
        };
    }

    // Precio adicional (ejemplo)
    public function extraCost(): float
    {
        return match ($this) {
            self::THREE_LAYERS => 3.50,
            self::STUFFED_MOZZARELLA_CHILI,
            self::STUFFED_CHEDDAR,
            self::STUFFED_MOZZARELLA_TOMATO => 2.00,
            default => 0.00, // Bases clásica/fina sin sobrecoste
        };
    }
}

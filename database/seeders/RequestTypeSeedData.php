<?php

namespace Database\Seeders;

final class RequestTypeSeedData
{
    public static function all(): array
    {
        return [
            // DIGITAL
            ['service' => 'design', 'key' => 'flyer_rrss', 'label' => 'Flyer para RRSS'],
            ['service' => 'design', 'key' => 'rrss_cover', 'label' => 'Portada para redes sociales'],
            ['service' => 'design', 'key' => 'carousel', 'label' => 'Carrusel de imágenes'],
            ['service' => 'design', 'key' => 'infographic_product', 'label' => 'Infografía de producto'],
            ['service' => 'design', 'key' => 'infographic_installation', 'label' => 'Infografía de instalación'],
            ['service' => 'design', 'key' => 'infographic_other', 'label' => 'Infografía otro'],
            ['service' => 'design', 'key' => 'invitation_ccad', 'label' => 'Invitación para CCAD'],
            ['service' => 'design', 'key' => 'invitation_cert', 'label' => 'Invitación para Certificación'],
            ['service' => 'design', 'key' => 'image_editing', 'label' => 'Edición de imagen'],
            ['service' => 'design', 'key' => 'seminar', 'label' => 'Seminario'],
            ['service' => 'design', 'key' => 'presentation', 'label' => 'Presentación'],
            ['service' => 'design', 'key' => 'tech_sheet', 'label' => 'Fichas técnicas'],
            ['service' => 'design', 'key' => 'distributor_brochure', 'label' => 'Brochure para distribuidor'],
            ['service' => 'design', 'key' => 'distributor_rrss', 'label' => 'Contenido para distribuidor RRSS'],
            ['service' => 'design', 'key' => 'distributor_catalog', 'label' => 'Catálogo de códigos para distribuidor'],
            ['service' => 'design', 'key' => 'digital_stationery', 'label' => 'Papelería Corporativa Digital'],

            // IMPRESO
            ['service' => 'design', 'key' => 'tarp_spider', 'label' => 'Lona para display araña'],
            ['service' => 'design', 'key' => 'tarp_large_format', 'label' => 'Lona gran formato'],
            ['service' => 'design', 'key' => 'tarp_banner', 'label' => 'Lona para banner'],
            ['service' => 'design', 'key' => 'vinyl', 'label' => 'Vinil'],
            ['service' => 'design', 'key' => 'product_brochure', 'label' => 'Brochure Productos'],
            ['service' => 'design', 'key' => 'flyers_print', 'label' => 'Folletería'],
            ['service' => 'design', 'key' => 'expo_stand', 'label' => 'Stand para expo'],
            ['service' => 'design', 'key' => 'warehouse_labels', 'label' => 'Etiquetas para almacén'],
            ['service' => 'design', 'key' => 'product_labels', 'label' => 'Etiquetas para producto individual'],
            ['service' => 'design', 'key' => 'silkscreen', 'label' => 'Serigrafía'],
            ['service' => 'design', 'key' => 'business_card_paper', 'label' => 'Tarjeta de Presentación papel'],
            ['service' => 'design', 'key' => 'business_card_pvc', 'label' => 'Tarjeta de Presentación PVC'],
            ['service' => 'design', 'key' => 'badges_pvc', 'label' => 'Gafetes PVC'],
            ['service' => 'design', 'key' => 'letterhead_legal', 'label' => 'Hoja membretada tamaño oficio'],
            ['service' => 'design', 'key' => 'letterhead_letter', 'label' => 'Hoja membretada tamaño carta'],

            ['service' => 'design', 'key' => 'other', 'label' => 'Otro'],

            ['service' => 'video', 'key' => 'corporate', 'label' => 'Video corporativo'],
            ['service' => 'video', 'key' => 'reel', 'label' => 'Reel'],
            ['service' => 'video', 'key' => 'editing', 'label' => 'Edición de material'],
            ['service' => 'video', 'key' => 'other', 'label' => 'Otro (Video)'],

            ['service' => 'render', 'key' => 'correction', 'label' => 'Corrección de render'],
            ['service' => 'render', 'key' => 'diagram', 'label' => 'Diagrama 3D'],
            ['service' => 'render', 'key' => 'product', 'label' => 'Render de producto'],
            ['service' => 'render', 'key' => 'other', 'label' => 'Otro (Render 3D)'],
        ];
    }
}

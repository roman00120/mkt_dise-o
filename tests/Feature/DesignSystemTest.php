<?php

namespace Tests\Feature;

use Tests\TestCase;

class DesignSystemTest extends TestCase
{
    public function test_design_system_loads_in_local_environment(): void
    {
        config(['app.env' => 'local']);

        $this->get('/design-system')
            ->assertOk()
            ->assertSee('TG Creative Hub')
            ->assertSee('TOTAL GROUND')
            ->assertSee('Catálogo de producto A500')
            ->assertSee('Correcciones solicitadas');
    }

    public function test_design_system_is_not_available_outside_local_environment(): void
    {
        config(['app.env' => 'production']);

        $this->get('/design-system')->assertNotFound();
    }

    public function test_design_system_renders_core_component_content(): void
    {
        config(['app.env' => 'local']);

        $response = $this->get('/design-system');

        $response->assertOk()
            ->assertSee('Primary')
            ->assertSee('Solicitudes activas')
            ->assertSee('Arrastra archivos aquí')
            ->assertSee('Nota interna')
            ->assertSee('Servicio');
    }
}

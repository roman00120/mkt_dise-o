# Validación

`WizardStepRequest` valida cada paso en backend. Los tipos pertenecen al catálogo del servicio seleccionado. `Otro` requiere especificación. El brief exige título y descripción; los campos específicos se validan según Diseño, Video o Render. La fecha no puede ser anterior a hoy. Urgente exige justificación.

La interfaz usa Alpine sólo para interacción inmediata; la autorización y las reglas viven en PHP.


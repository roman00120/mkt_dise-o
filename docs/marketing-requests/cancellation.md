# Cancelación

Marketing puede cancelar `pending`, `in_validation` y `waiting_for_information`. El motivo es obligatorio y tiene máximo 1,000 caracteres. La acción usa modal, POST, policy, lock transaccional, estado `cancelled`, fecha, motivo y evento. No se permite revertirla ni cancelar borradores.

# Máquina de estados

La fuente de verdad es `RequestTransitionService`. Transiciones: pending→in_validation; in_validation→assigned/waiting_for_information/rejected; waiting_for_information→in_validation/assigned; assigned→in_progress/waiting_for_information; in_progress→waiting_for_information/internal_review; internal_review→in_progress/marketing_review. El navegador nunca escribe estados arbitrarios.

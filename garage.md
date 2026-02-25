# Signature Garage — Especificación de Aplicación

> **Versión:** 1.0  
> **Fecha:** Febrero 2026  
> **Cliente:** Signature Garage (SG) — Punta del Este, Uruguay  
> **Desarrollador:** Latitud Nómade  

---

## 1. Visión General

### 1.1 ¿Qué es?

Aplicación web progresiva (PWA) para la gestión integral del taller, guardería de vehículos y base de clientes de Signature Garage. La aplicación es **la fuente única de verdad** del negocio: todo lo que pasa, se registra acá. Nada queda en WhatsApp, papelitos ni planillas.

### 1.2 ¿Para quién es?

Signature Garage es una automotora boutique en Punta del Este especializada en vehículos premium, deportivos y clásicos (Porsche, BMW, Audi, Mercedes, Land Rover). Opera en tres líneas de negocio:

- **Taller / Posventa** — Reparación, mantenimiento y preparación mecánica
- **Guardería de vehículos** — Almacenamiento con servicios periódicos (~40 autos)
- **Venta de usados** — Preparación mecánica y publicación de vehículos

El taller trabaja **principalmente con autos propios** (guardería + ventas), con algunos trabajos de clientes externos. Volumen mensual de aproximadamente 30-40 vehículos.

### 1.3 ¿Qué problema resuelve?

Hoy la operación se maneja con papel, WhatsApp y memoria. Los dolores principales son:

- **Se escapan tareas sin hacer** — Un auto viene por 14 ítems, se hacen 13, se entrega sin el restante. Ejemplo real: se olvidaron un corte de batería, perdieron 25-30 USD.
- **Se escapan costos sin cobrar** — Insumos pequeños (aceite, tornillos, cortes) que no se registran y se pierden.
- **No hay visibilidad del estado del taller** — No se sabe qué tiene pendiente cada auto, en qué etapa está, qué repuestos faltan.
- **Guardería sin control** — 40 autos, pagos mensuales, servicios periódicos, todo de memoria.
- **No hay historial** — Cuando un auto vuelve, no hay registro de qué se le hizo antes.
- **Cobros pendientes se pierden** — No hay forma automatizada de saber quién pagó y quién no.

---

## 2. Actores / Roles de Usuario

### 2.1 Mecánico / Taller

Es el operario que trabaja en los vehículos. Usa la app desde su celular.

**Puede:**
- Ver los trabajos asignados y su cola de prioridad (semáforo)
- Ver el checklist de tareas de cada trabajo
- Agregar diagnósticos mecánicos
- Solicitar pedidos de repuestos
- Cargar horas de trabajo (horas + tarea descriptiva + operario)
- Cargar insumos/pequeños materiales
- Marcar ítems del checklist como completados
- Cerrar la orden de taller (con validación de checklist)
- Cambiar ubicación del vehículo

**NO puede:**
- Ver montos, costos ni precios de nada
- Modificar ítems originales del motivo
- Editar una orden ya cerrada
- Acceder al módulo de administración ni facturación
- Ver adelantos ni pagos

### 2.2 Administración

Personal administrativo que gestiona compras, costos, facturación y cobros.

**Puede:**
- Todo lo que ve el mecánico (en lectura)
- Gestionar el módulo de repuestos (cotizar, encargar, marcar recibido)
- Cargar costos de repuestos (monto, IVA, tipo, proveedor)
- Cargar servicios tercerizados
- Registrar adelantos/entregas de clientes
- Revisar y ajustar la cuenta al cierre (horas facturables, montos, cortesías, descuentos)
- Generar PDF de cuenta y enviarlo al cliente
- Generar y enviar link de presupuesto para aprobación del cliente
- Cambiar estado a "Cuenta Pasada a Cliente" y "Cobrado"
- Reabrir una orden cerrada por el taller
- Gestionar pagos de guardería
- Gestionar clientes

### 2.3 Socio / Director (Germán, Peri/Pedro)

Los dueños. Tienen acceso total y configuran el sistema.

**Puede:**
- Todo lo de Administración
- Configurar márgenes y valores (valor hora taller, % repuestos, % servicios)
- Gestionar usuarios y roles
- Acceder a reportes globales
- Crear y administrar ubicaciones, proveedores, operarios
- Gestionar módulo de guardería y ventas
- Publicar vehículos a la web (fase futura)

---

## 3. Entidades Principales

### 3.1 Vehículo *(entidad central — todo gira alrededor del vehículo)*

El vehículo es la entidad raíz. La deuda es con el vehículo, no con el cliente. Un cliente puede tener múltiples vehículos.

| Campo | Tipo | Detalle |
|-------|------|---------|
| VIN | String (único) | Identificador principal |
| Marca | String | Porsche, BMW, Audi, Mercedes, etc. |
| Modelo | String | 911, X5, A4, etc. |
| Año | Integer | Año del vehículo |
| Kilometraje | Integer | Último kilometraje registrado |
| Categoría | Enum | `GUARDERIA`, `VENTA`, `EXTERNO` |
| Cliente propietario | Relación | FK a Cliente |
| Fotos | Archivos | Fotos del vehículo |
| Notas | Texto | Observaciones generales del vehículo |
| Estado guardería | Enum | `ACTIVO`, `INACTIVO` (solo si categoría = GUARDERIA) |
| Fecha ingreso guardería | Date | Solo si categoría = GUARDERIA |
| QR Code | Generado | Código QR con link al historial |

### 3.2 Cliente

| Campo | Tipo | Detalle |
|-------|------|---------|
| Nombre | String | Nombre completo |
| Teléfono | String | Contacto principal |
| Email | String | Para notificaciones |
| Fecha de cumpleaños | Date | Para saludo automatizado — importantísimo |
| Cédula | String | Documento de identidad |
| Notas | Texto | Observaciones |
| Tipo | Enum | `NORMAL`, `CARGO_INTERNO_TALLER`, `CARGO_INTERNO_SOCIO`, `CARGO_INTERNO_VENTAS`, `CARGO_INTERNO_CORTESIA` |
| Último servicio | Date | Calculado del último trabajo cerrado |

Clientes especiales para cargos internos: **Taller** (errores del mecánico), **Socio [Nombre]** (uso personal de socios), **Ventas** (preparación de autos para vender), **Cortesía** (trabajos gratuitos).

### 3.3 Orden de Trabajo *(entidad más compleja)*

Detallada en la sección de Módulos (sección 4.3).

### 3.4 Proveedor

| Campo | Tipo | Detalle |
|-------|------|---------|
| Nombre | String | Razón social o nombre comercial |
| Contacto | String | Teléfono / email |
| Dirección | String | Ubicación física |
| Tipo de servicio | String | Qué ofrece |
| Es ubicación | Boolean | Si el vehículo puede estar físicamente ahí |

### 3.5 Ubicación

| Campo | Tipo | Detalle |
|-------|------|---------|
| Nombre | String | Nombre descriptivo |
| Tipo | Enum | `TALLER_PROPIO`, `PROVEEDOR_TERCERO`, `CASA_CLIENTE` |
| Proveedor asociado | Relación | FK a Proveedor (si aplica) |

### 3.6 Operario

| Campo | Tipo | Detalle |
|-------|------|---------|
| Nombre | String | Nombre del mecánico/operario |
| Activo | Boolean | Si está disponible para asignar horas |

---

## 4. Módulos

### 4.1 Módulo de Vehículos

**Propósito:** Gestión centralizada de todos los vehículos que pasan por SG. Es el master de datos de vehículos.

**Funcionalidades:**
- CRUD completo de vehículos
- Asignación de categoría: Guardería, Venta, Externo
- Asociación con cliente propietario
- Historial completo de todos los trabajos/órdenes realizados
- Fotos del vehículo
- Notas generales
- Generación de código QR con historial (acceso limitado para el cliente)
- Filtrado por categoría, estado, ubicación actual
- Registro de kilometraje

### 4.2 Módulo de Clientes / CRM Básico

**Propósito:** Base de datos de clientes con funcionalidades mínimas de CRM para mantener contacto.

**Funcionalidades:**
- CRUD completo de clientes
- Registro de fecha de cumpleaños
- Registro de último servicio (calculado automáticamente)
- Vehículos asociados (relación 1 a muchos)
- Historial de todos los trabajos asociados a sus vehículos
- Notas del cliente
- Clientes especiales para cargos internos (precargados en el sistema)

**Automatizaciones (fase futura pero diseñar la base):**
- Saludo de cumpleaños automático (WhatsApp o email)
- Aviso de servicio periódico por tiempo (los autos en Punta del Este se usan poco, el servicio es por tiempo más que por km)
- Puntos de contacto periódicos automatizados

### 4.3 Módulo de Órdenes de Trabajo *(corazón de la aplicación)*

**Propósito:** Gestionar el ciclo de vida completo de cada trabajo mecánico, desde que se detecta una necesidad hasta que se cobra.

**Campos de la Orden:**

| Campo | Tipo | Detalle |
|-------|------|---------|
| Número de orden | Auto-generado | Identificador único secuencial |
| Vehículo | Relación | FK a Vehículo |
| Cliente | Relación | FK a Cliente |
| Motivo | Texto (obligatorio) | El detonante — lo que pide el cliente o se diagnosticó |
| Checklist de tareas | Lista de ítems | Desglose del motivo en tareas individuales |
| Estado | Enum | Ver flujo de estados |
| Prioridad | Enum | `ROJO` (urgente), `AMARILLO` (importante), `VERDE` (puede esperar) |
| Ubicación actual | Relación | FK a Ubicación |
| Fecha de creación | Timestamp | Automático |
| Fecha de agendado | Date | Opcional — la agenda pierde protagonismo |
| Fecha de ingreso | Timestamp | Cuando el auto llega físicamente |
| Fecha de inicio proceso | Timestamp | Cuando arranca el trabajo |
| Fecha de cierre taller | Timestamp | Cuando el mecánico cierra |
| Fecha de cobrado | Timestamp | Cuando se confirma el pago |
| Observaciones de cierre | Texto | Lo que deja el mecánico al cerrar |
| Orden origen | Relación | FK a otra Orden (si fue generada automáticamente) |

**Estados de la Orden:**

```
CREADO → AGENDADO (opcional) → INGRESO → EN_PROCESO → EN_ESPERA_REPUESTO (temporal) → CIERRE_TALLER → CUENTA_PASADA_CLIENTE → COBRADO
```

| Estado | Descripción | Responsable |
|--------|-------------|-------------|
| `CREADO` | Pre-orden. El trabajo existe pero no tiene fecha ni se empezó | Admin/Encargado |
| `AGENDADO` | Se coordinó fecha con el cliente (opcional, pierde relevancia) | Admin/Encargado |
| `INGRESO` | El auto llegó. Se hace inspección visual + electrónica | Taller |
| `EN_PROCESO` | Trabajo activo. Se cargan diagnósticos, horas, repuestos, insumos | Taller |
| `EN_ESPERA_REPUESTO` | Stand-by. No se puede avanzar hasta que llegue una pieza crítica | Taller/Admin |
| `CIERRE_TALLER` | El mecánico terminó. Queda bloqueado. Pasa a administración | Taller → Admin |
| `CUENTA_PASADA_CLIENTE` | La cuenta se armó y se envió al cliente. Pendiente de cobro | Admin |
| `COBRADO` | El cliente pagó. Trabajo finalizado | Admin |

**Reglas de negocio críticas:**

1. **Motivos iniciales inmutables.** Los ítems originales del checklist no se pueden borrar ni editar. Solo se pueden agregar nuevos ítems.

2. **Prioridad por semáforo.** Cada trabajo tiene una prioridad (🔴🟡🟢). El jefe de taller limpia primero los rojos, luego amarillos, luego verdes.

3. **Checklist obligatorio al cierre.** Para cerrar la orden, el mecánico DEBE tener todos los ítems del checklist marcados como hechos. Si hay alguno sin marcar, debe seleccionar un motivo:
   - No se encuentra repuesto → No genera nuevo trabajo
   - Cliente no acepta presupuesto → No genera nuevo trabajo
   - No es para SG → No genera nuevo trabajo
   - Demora en importación de repuesto → **Genera nuevo trabajo automáticamente**
   - Reagenda → **Genera nuevo trabajo automáticamente**
   - Otros → Campo texto obligatorio

4. **Bloqueo al cerrar.** Una vez que el mecánico cierra, no puede tocar nada. Solo admin puede reabrir.

5. **Notificación automática.** Al cerrar el taller, se notifica a administración que el trabajo está listo para facturar.

6. **Generación automática de nuevo trabajo.** Si un ítem queda pendiente por demora de repuesto o reagenda, se crea automáticamente un trabajo nuevo vinculado al anterior, con el motivo correspondiente y el adelanto si existe.

7. **Horas del taller ≠ Horas facturadas.** Las horas que carga el mecánico quedan congeladas. Administración define por separado cuántas horas factura. Ambos datos se guardan para reportes de eficiencia.

8. **El mecánico nunca ve montos.** Ni costos, ni precios, ni márgenes. Solo carga qué hizo y con qué.

### 4.4 Módulo de Inspección de Ingreso

**Propósito:** Registro del estado del vehículo al momento de llegar al taller. Es un "paraguas legal" — protege al taller de reclamos futuros.

**Funcionalidades:**
- Fotos obligatorias del vehículo: frente, costados, trasera, interior, detalle de roturas
- Adjuntar PDF del scanner electrónico (OBD) — registra fallas electrónicas existentes al ingreso
- Opción de anular inspección visual o scanner con motivo obligatorio (ej: "vehículo clásico, no aplica scanner")
- Todo queda registrado con fecha y usuario
- Al completar la inspección, el trabajo pasa automáticamente a "En Proceso"

**Contexto:** En autos modernos, el scanner genera un PDF con todas las fallas electrónicas. Esto sirve para que cuando el cliente retire el auto y diga "esto me lo rompieron ustedes", se pueda demostrar que la falla ya existía al ingreso.

### 4.5 Módulo de Diagnóstico Mecánico

**Propósito:** Registro de los hallazgos del mecánico a medida que trabaja en el vehículo.

**Funcionalidades:**
- Campo de texto extenso para cada diagnóstico
- Múltiples diagnósticos por trabajo (el mecánico va encontrando cosas)
- Fecha y usuario automáticos en cada entrada
- Historial visible de diagnósticos anteriores (para no repetir)
- Botón "Enviar diagnóstico" — el mecánico decide cuándo notifica a admin, NO cada vez que escribe
- Opción de excepción de diagnóstico con justificación (cuando no aplica, ej: cambio de manija simple)

**Contexto:** Es como ir al médico. Entrás por un dolor de cabeza y te diagnostican tres cosas más. Lo mismo con los autos: viene por un ruido y le encuentran pastillas gastadas, amortiguadores en mal estado y un cable roto.

### 4.6 Módulo de Solicitud de Pedidos

**Propósito:** El mecanismo formal para que el mecánico pida piezas/repuestos.

**Funcionalidades:**
- Campo de texto: qué pieza necesita (descripción, no código)
- Solicitud en bulk (múltiples piezas en un solo pedido)
- Fecha automática de solicitud
- Notificación a administración (email + push)
- Historial visible de todo lo pedido en ese trabajo
- El pedido está **asociado al trabajo pero vive por fuera** — se gestiona en el módulo de repuestos

**Importante:** El pedido NO tiene costo. Es puramente descriptivo. El costo lo maneja administración en el módulo de repuestos.

### 4.7 Módulo de Repuestos *(gestión administrativa)*

**Propósito:** Seguimiento pieza por pieza de todo lo que se pide, desde la solicitud hasta la instalación.

**Flujo de un repuesto:**

```
SOLICITADO (mecánico pide) → COTIZADO (admin busca precio) → ENCARGADO (admin compra) → EN_VIAJE → RECIBIDO_BODEGA (llegó al taller) → ENTREGADO_MECANICO (mecánico lo agarra) → INSTALADO (mecánico lo carga a la orden)
```

Si no se consigue: `SOLICITADO → NO_DISPONIBLE` (notificación al taller)

**Campos por repuesto:**

| Campo | Tipo | Detalle |
|-------|------|---------|
| Descripción | Texto | Qué pieza es |
| Trabajo asociado | Relación | FK a Orden de Trabajo |
| Vehículo | Relación | FK a Vehículo |
| Estado | Enum | Ver flujo arriba |
| Tipo | Enum | `IMPORTADO_POR_SG`, `COMPRA_DE_PLAZA` |
| Costo sin IVA | Decimal | Solo visible para admin |
| Es más IVA | Boolean | Checkbox |
| Proveedor | Relación | FK a Proveedor |
| Fecha solicitado | Timestamp | Automática |
| Fecha encargado | Timestamp | Cuando admin lo compra |
| Fecha recibido | Timestamp | Cuando llega al taller |
| Fecha entregado mecánico | Timestamp | Cuando el meca lo agarra |
| Solicitado por | Relación | FK a Usuario |

**Funcionalidades clave:**
- Vista global de todos los repuestos pendientes (filtrable por estado, trabajo, vehículo)
- Notificación al taller cuando un repuesto no se consigue
- Si el trabajo se cierra con repuesto pendiente → migra al nuevo trabajo automático
- Reportes: tiempos de demora (pedido → recibido), importados vs. plaza, gasto por proveedor

### 4.8 Módulo de Carga de Horas

**Propósito:** Registro del tiempo que destina cada mecánico a cada tarea.

**Campos por entrada de horas:**

| Campo | Tipo | Detalle |
|-------|------|---------|
| Horas | Decimal (base 10) | Mínimo 0.1 (no base 60). Ej: 0.2 horas, 1.5 horas |
| Tarea | Texto (obligatorio) | Qué se hizo en esas horas |
| Operario | Relación | FK a Operario (seleccionable de lista administrable) |
| Trabajo | Relación | FK a Orden de Trabajo |
| Fecha | Timestamp | Automática |

**Comportamiento:**
- Se acumula un total de horas por trabajo, visible en todo momento
- Cada entrada individual queda registrada con su detalle
- El mecánico va cargando a medida que trabaja: "2 horas — cambio de aceite", "0.2 horas — corte de corriente"
- Los decimales son base 10: 1.1, 1.2, 1.3, etc. NO base 60 (minutos)

**Ejemplo real (servicio oficial):**
```
0.2 horas — Corte de corriente
0.8 horas — Diagnóstico general  
2.0 horas — Cambio de aceite y filtros
0.5 horas — Revisión de frenos
─────────
3.5 horas — Total trabajo
```

### 4.9 Módulo de Carga de Insumos / Pequeños Materiales

**Propósito:** Registro de materiales menores que el taller usa y que tienen costo (pero no son repuestos grandes).

**Campos:**

| Campo | Tipo | Detalle |
|-------|------|---------|
| Descripción | Texto | Qué insumo es (corte de batería, aceite, tornillo, líquido aditivo) |
| Costo | Decimal | Monto (solo visible admin) |
| Trabajo | Relación | FK a Orden de Trabajo |
| Fecha | Timestamp | Automática |
| Cargado por | Relación | FK a Usuario |

**Contexto:** Esto es lo que más se escapa hoy. Un corte de batería de 25 USD, un litro de aceite, tornillos. Cosas que el mecánico pone y nadie registra. La app tiene que hacer que el mecánico cargue todo sin fricciones.

### 4.10 Módulo de Servicios Tercerizados

**Propósito:** Registro de trabajos que se mandan a hacer afuera (escapero, tapicero, electricista, etc.)

**Campos:**

| Campo | Tipo | Detalle |
|-------|------|---------|
| Descripción | Texto | Qué se hizo |
| Costo sin IVA | Decimal | Monto |
| Es más IVA | Boolean | Checkbox |
| Proveedor | Relación | FK a Proveedor |
| Trabajo | Relación | FK a Orden de Trabajo |
| Fecha | Timestamp | Automática |

### 4.11 Módulo de Adelantos / Entregas

**Propósito:** Registro de pagos parciales del cliente durante el proceso del trabajo.

**Campos:**

| Campo | Tipo | Detalle |
|-------|------|---------|
| Monto | Decimal | Cantidad entregada |
| Fecha | Date | Cuándo se recibió |
| Justificación | Texto | Por qué concepto (ej: "Adelanto por importación de capota") |
| Trabajo | Relación | FK a Orden de Trabajo |

**Comportamiento:**
- Puede existir desde la creación del trabajo (antes de que el auto llegue)
- No depende de ninguna fase/estado
- Se descuenta del total al momento de armar la cuenta
- Si se genera un trabajo nuevo automático, el adelanto asociado al repuesto pendiente se transfiere

### 4.12 Módulo de Guardería

**Propósito:** Gestión del servicio de almacenamiento de vehículos. Con el local nuevo, van a manejar ~40 autos y es imposible hacerlo de memoria.

**Funcionalidades:**
- Registro de vehículos en guardería (usa el mismo módulo de Vehículos con categoría GUARDERIA)
- Propietario, kilometraje, fecha de ingreso
- **Pagos mensuales:** quién está al día, quién no, pendientes de cobro
- **Notas y pendientes:** observaciones del vehículo, cosas a tener en cuenta
- **Fechas de servicios:** cuándo fue el último servicio, cuándo toca el próximo (por tiempo, no por km, porque en Punta del Este los autos se usan poco)
- **Generación de órdenes de taller** desde la ficha de guardería
- Vista tipo semáforo: servicios al día (verde), próximos a vencer (amarillo), vencidos (rojo)

**Flujo guardería → taller:**
Un auto de guardería que tiene algo pendiente genera una orden de trabajo normal. El taller trabaja sobre ese auto como cualquier otro. Al cerrar, el historial queda en el vehículo.

**Pagos de guardería:**

| Campo | Tipo | Detalle |
|-------|------|---------|
| Vehículo | Relación | FK a Vehículo |
| Mes | Date | Período del pago |
| Monto | Decimal | Valor de la guardería |
| Estado | Enum | `PENDIENTE`, `PAGADO` |
| Fecha de pago | Date | Cuándo pagó |
| Notas | Texto | Observaciones |

### 4.13 Módulo de Administración y Facturación

**Propósito:** Gestión financiera de las órdenes cerradas. Ajuste de cuentas, facturación y cobro.

**Vista de trabajos cerrados (separada de la vista de taller):**

Cuando el taller cierra una orden, aparece acá con una vista enfocada en números.

**Funcionalidades:**
- Ver resumen de costos: horas, repuestos, servicios, insumos
- **Horas facturables** — editables, distintas a las horas del taller (las del taller quedan congeladas)
- **Todos los montos editables** solo por el administrador
- Aplicar descuentos, cortesías (valor cero), ajustes
- **Configuración de márgenes (settings globales):**
  - Valor hora de taller (USD)
  - % margen sobre repuestos
  - % margen sobre servicios tercerizados
- Resumen automático: **costo total vs. valor de facturación estimado**
- **Generación de PDF** de la cuenta para el cliente
- **Generación de link de presupuesto** para aprobación del cliente (trackeable: si lo abrió, cuándo, si aprobó)
- Cambio de estado a "Cuenta Pasada a Cliente"
- Confirmación de cobro → "Cobrado"
- Posibilidad de **reabrir** una orden al taller si falta algo

**Cálculo automático de cuenta:**

```
COSTO:
  Horas taller × Valor hora          = Costo mano de obra
  Σ Repuestos (costo sin IVA)        = Costo repuestos
  Σ Servicios terceros (costo sin IVA) = Costo servicios
  Σ Insumos                          = Costo insumos
  ─────────────────────────────────────
  COSTO TOTAL

FACTURACIÓN:
  Horas facturables × Valor hora     = Factura mano de obra
  Repuestos × (1 + % margen)         = Factura repuestos  
  Servicios × (1 + % margen)         = Factura servicios
  Insumos (puede tener margen o no)  = Factura insumos
  ─────────────────────────────────────
  FACTURA TOTAL
  - Adelantos ya entregados
  ─────────────────────────────────────
  SALDO A COBRAR
```

### 4.14 Módulo de Ubicaciones

**Propósito:** Saber dónde está cada vehículo en todo momento.

**Funcionalidades:**
- Lista administrable de ubicaciones
- Tipos: taller propio, proveedor/tercero, casa del cliente
- Asociable a proveedor
- **Historial de movimientos** por vehículo: cada cambio queda registrado con fecha
- Cambio de ubicación disponible en todo momento durante el proceso
- Filtrar: ¿qué autos tengo acá? ¿qué autos están afuera?

### 4.15 Módulo de Proveedores

**Propósito:** Registro de terceros con los que trabaja SG.

**Funcionalidades:**
- CRUD de proveedores
- Asociable a ubicaciones, servicios tercerizados y repuestos
- Para reportes futuros: gasto mensual/anual por proveedor

### 4.16 Módulo de Pendientes de Cobro

**Propósito:** Vista consolidada de todo lo que hay para cobrar.

**Funcionalidades:**
- Lista automática de todas las órdenes en estado "Cuenta Pasada a Cliente"
- Monto total pendiente
- Desglose por trabajo, cliente, antigüedad
- Adelantos ya recibidos descontados
- Pendientes de guardería
- Al confirmar cobro → se saca automáticamente de la lista
- Link de pago para cobro online (fase futura)

### 4.17 Módulo de Notificaciones

**Propósito:** Mantener informados a los actores relevantes en cada paso.

**Notificaciones del sistema:**

| Evento | De | Para | Canal |
|--------|----|------|-------|
| Diagnóstico enviado | Taller | Admin | Push + Email |
| Solicitud de pedido | Taller | Admin | Push + Email |
| Cierre de taller | Taller | Admin | Push + Email |
| Repuesto no disponible | Admin | Taller | Push |
| Orden reabierta | Admin | Taller | Push |
| Presupuesto enviado al cliente | Sistema | Cliente | Email/WhatsApp + Link |
| Cuenta pasada al cliente | Sistema | Cliente | Email/WhatsApp + Link |
| Pago recibido | Sistema | Admin | Push |
| Servicio de guardería próximo | Sistema | Admin | Push |
| Cumpleaños de cliente | Sistema | Admin | Push (fase futura: auto WhatsApp) |

### 4.18 Módulo de QR e Historial para Cliente

**Propósito:** Dar al cliente acceso limitado al estado de su vehículo.

**Funcionalidades:**
- Generación de QR por trabajo
- El cliente escanea y ve:
  - Estado actual del trabajo (en qué fase está)
  - En qué etapa: diagnóstico, reparación, espera de repuesto, pronto para entregar
- Historial del vehículo (vista limitada — solo trabajos pasados con descripción general)
- PDF del scanner adjunto si aplica
- **NO ve:** costos internos, diagnósticos detallados, horas, márgenes

**Fase futura:** QR imprimible en sticker para el parabrisas del auto.

### 4.19 Módulo de Reportes *(fase 2, cuando haya datos)*

**Propósito:** Inteligencia operativa para tomar decisiones.

**Reportes planificados:**
- **Eficiencia del taller:** horas trabajadas vs. horas facturadas
- **Producción en proceso:** órdenes abiertas vs. autos físicamente en taller (detectar bola de nieve de órdenes fantasma)
- **Tiempos de repuestos:** desde pedido hasta recibido (por proveedor, por tipo)
- **Costos por vehículo:** cuánto cuesta cada auto en total
- **Gasto por proveedor:** mensual/anual
- **Importados vs. plaza:** volumen y costo de repuestos importados por SG vs. compra local
- **Cargos internos:** desglose por categoría (taller, socios, ventas, cortesía)
- **Pendientes de cobro:** antigüedad, montos
- **Servicios de guardería:** vencimientos, cumplimiento
- **CRM:** contactos realizados, cumpleaños enviados, tasa de retorno de clientes

---

## 5. Flujo Completo del Negocio

### 5.1 Ciclo de Vida de una Orden de Trabajo

#### FASE 1: CREACIÓN

**Quién:** Admin/Encargado  
**Trigger:** Un cliente llama, un auto de guardería necesita algo, un auto de venta necesita preparación.

1. Se crea la orden con **motivo obligatorio** — es el detonante de todo.
2. El motivo se desglosa en **ítems individuales** → checklist.
3. Se asocia **cliente** y **vehículo**.
4. Se asigna **prioridad** (🔴🟡🟢).
5. Opcionalmente se registra un **adelanto**.
6. Estado: **CREADO** — flota en la cola del taller.

> *Ejemplo: Auto de guardería tiene ruido en el tren delantero y necesita inflar ruedas. Ítems: "Diagnosticar ruido tren delantero" + "Inflar ruedas". Prioridad: 🟡 amarillo.*

#### FASE 2: AGENDADO (opcional)

**Quién:** Admin/Encargado

- Se coordina fecha con el cliente (solo relevante para clientes externos).
- Para autos cautivos (guardería/venta) **no se agenda**, se maneja por prioridad.
- Estado: **AGENDADO**
- Puede saltarse directamente a Ingreso.

#### FASE 3: INGRESO E INSPECCIÓN

**Quién:** Taller

Cuando el auto llega físicamente al taller o se decide empezar a trabajar.

1. Estado cambia a **INGRESO**.
2. La app guía la inspección paso a paso:

```
Paso 1: Fotos del vehículo
  → Tomar fotos (frente, costados, trasera, interior, roturas)
  → Si no aplica: motivo obligatorio

Paso 2: PDF del scanner electrónico
  → Adjuntar PDF del OBD
  → Si no aplica (clásico): motivo obligatorio

Paso 3: Asignar ubicación actual
```

3. Al completar → pasa automáticamente a **EN_PROCESO**.

#### FASE 4: EN PROCESO

**Quién:** Mecánico (trabajo diario) + Admin (gestión de repuestos y costos en paralelo)

El auto está en el taller, se trabaja activamente. Acá conviven todas las acciones:

**El mecánico:**
- Ve su cola de trabajos ordenada por prioridad (semáforo)
- Abre un trabajo y ve el checklist de tareas
- Trabaja en un ítem → carga horas + descripción
- Encuentra algo nuevo → agrega diagnóstico → envía cuando está listo
- Necesita pieza → solicita pedido → notifica a admin
- Instala repuesto que le entregan → lo da de alta en la orden
- Carga insumos/materiales pequeños que usa
- Marca ítems como ✅ completados
- Puede cambiar la ubicación del vehículo en cualquier momento

**Administración en paralelo:**
- Recibe solicitudes de pedido
- Busca, cotiza, encarga repuestos
- Actualiza el status de cada repuesto
- Cuando llega un repuesto → lo marca como "recibido en bodega"
- Carga costos de repuestos, servicios tercerizados
- Registra adelantos si el cliente entrega dinero
- Puede generar y enviar link de presupuesto al cliente para aprobación de trabajos adicionales

**Resumen visible en todo momento:**
```
Total horas cargadas: X.X
Total repuestos: $XX (solo admin)
Total servicios: $XX (solo admin)
Total insumos: $XX (solo admin)
Estimado de facturación: $XX (solo admin)
Ítems completados: 8/12
```

#### FASE 4.5: EN ESPERA DE REPUESTO

**Quién:** Taller/Admin

Cuando ya no hay más que hacer hasta que llegue una pieza crítica:

- Mecánico marca "En espera de repuesto" → estado cambia.
- El auto puede quedarse en taller o irse a casa del cliente (cambio de ubicación).
- Cuando llega el repuesto → admin reactiva → vuelve a **EN_PROCESO**.
- Permite medir producción real vs. autos parados.

#### FASE 5: CIERRE DE TALLER

**Quién:** Mecánico → Admin

**Proceso de cierre:**

```
Mecánico presiona "Cerrar Taller"
    │
    ├── ¿Todos los ítems del checklist ✅?
    │     │
    │     ├── SÍ → Agregar observaciones finales → CIERRE OK
    │     │
    │     └── NO → "¿Estás seguro?"
    │           │
    │           ├── NO → Vuelve a En Proceso
    │           │
    │           └── SÍ → Por cada ítem pendiente, seleccionar motivo:
    │                 ├── No se encuentra repuesto → MUERE
    │                 ├── Cliente no acepta presupuesto → MUERE
    │                 ├── No es para SG → MUERE
    │                 ├── Demora en importación → GENERA NUEVO TRABAJO
    │                 ├── Reagenda → GENERA NUEVO TRABAJO
    │                 └── Otros → Texto obligatorio
    │
    └── RESULTADO:
        ├── Orden BLOQUEADA para taller
        ├── Notificación a admin: "Listo para facturar"
        └── Si aplica: nuevo trabajo creado automáticamente
```

#### FASE 6: ADMINISTRACIÓN Y FACTURACIÓN

**Quién:** Admin/Socios

1. El trabajo aparece en la sección **"Trabajos Cerrados"** con vista de números.
2. Admin ve el resumen: horas del taller (congeladas), repuestos, servicios, insumos.
3. Admin define **horas facturables** (pueden ser menos que las del taller).
4. Admin ajusta montos: descuentos, cortesías (valor cero), correcciones.
5. Los márgenes predefinidos se aplican automáticamente pero son editables por ítem.
6. Admin genera **PDF de la cuenta**.
7. Opcionalmente genera **link de presupuesto/cuenta** para enviar al cliente.
8. Estado cambia a **CUENTA_PASADA_CLIENTE**.
9. Si falta algo → admin puede **reabrir** al taller → vuelve a En Proceso.

#### FASE 7: COBRO

**Quién:** Admin

1. El cliente recibe la cuenta (PDF, link, o en persona).
2. Cuando paga → admin confirma → estado **COBRADO**.
3. Se saca automáticamente de pendientes de cobro.
4. Trabajo finalizado. ✅

**Fase futura:** Link de pago con pasarela (mercado pago, tarjeta) que al completarse cambia el estado automáticamente.

### 5.2 Flujo de Cargos Internos

```
Se crea una orden normal PERO:
  ├── Cliente = "Taller" (error del mecánico)
  ├── Cliente = "Socio - Germán" (uso personal)
  ├── Cliente = "Ventas" (preparación de auto para vender)
  └── Cliente = "Cortesía" (regalo al cliente)

Sigue el mismo flujo de taller pero NO se factura a un cliente externo.
Las horas y costos quedan registrados → reportes de eficiencia.
```

### 5.3 Flujo de Guardería

```
Alta de vehículo en guardería
  ├── Propietario, kilometraje, fecha ingreso
  ├── Definir costo mensual
  │
  ├── Pagos mensuales
  │   └── Pendiente → Pagado (con fecha)
  │
  ├── Servicios periódicos
  │   ├── Fecha del último servicio
  │   ├── Fecha del próximo servicio (calculada)
  │   └── Semáforo: 🟢 al día | 🟡 próximo | 🔴 vencido
  │
  ├── Cuando necesita trabajo mecánico:
  │   └── Se genera Orden de Trabajo normal → flujo de taller
  │
  └── Notas y observaciones del vehículo
```

---

## 6. Stack Tecnológico

### 6.1 Arquitectura General

```
┌─────────────────────────────────────────────────┐
│                   CLIENTE                        │
│                                                  │
│   PWA (Next.js)        ← App-like, responsive   │
│   React + TypeScript   ← UI components          │
│   Tailwind CSS         ← Styling                │
│   Zustand o React Query← State management       │
│                                                  │
└──────────────────┬──────────────────────────────┘
                   │ HTTPS / REST API
                   │ (o tRPC para type-safety)
┌──────────────────▼──────────────────────────────┐
│                   SERVIDOR                       │
│                                                  │
│   Next.js API Routes   ← Backend                │
│   (o Express si se     ← API REST               │
│    separa el backend)  ← Auth, Business Logic   │
│                                                  │
│   Prisma ORM           ← Acceso a datos         │
│   NextAuth.js          ← Autenticación          │
│                                                  │
└──────────────────┬──────────────────────────────┘
                   │
┌──────────────────▼──────────────────────────────┐
│               BASE DE DATOS                      │
│                                                  │
│   PostgreSQL           ← Base de datos principal │
│   (Railway Postgres)   ← Hosting                │
│                                                  │
└─────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────┐
│             SERVICIOS AUXILIARES                  │
│                                                  │
│   Railway              ← Hosting (app + DB)     │
│   Cloudflare R2 / S3   ← Almacenamiento archivos│
│                         (fotos, PDFs, QR)        │
│   Resend o SendGrid    ← Emails transaccionales │
│   WhatsApp Business API← Notificaciones (futuro)│
│   Stripe / MercadoPago ← Pasarela pago (futuro) │
│                                                  │
└─────────────────────────────────────────────────┘
```

### 6.2 Tecnologías Seleccionadas

| Capa | Tecnología | Justificación |
|------|-----------|---------------|
| **Framework** | Next.js 14+ (App Router) | Full-stack en un solo proyecto. SSR para SEO futuro (web de vehículos). API routes para el backend. Ideal para PWA. |
| **Lenguaje** | TypeScript | Type-safety end-to-end con Prisma. Menos bugs, mejor DX con Claude Code. |
| **UI** | React + Tailwind CSS + shadcn/ui | Componentes accesibles, mobile-first, app-like. Rápido de implementar. |
| **ORM** | Prisma | Type-safe queries, migraciones automáticas, schema declarativo. Excelente con TypeScript. |
| **Base de datos** | PostgreSQL | Relacional, robusto, perfecto para datos estructurados. Railway lo ofrece nativamente. |
| **Autenticación** | NextAuth.js (Auth.js) | Manejo de sesiones, roles, providers. Integra con Prisma nativamente. |
| **Hosting** | Railway | Deploy sencillo, PostgreSQL incluido, buen precio, auto-scaling. SSL incluido. |
| **Almacenamiento** | Cloudflare R2 (o Railway volume) | Fotos, PDFs del scanner, QR generados. R2 es S3-compatible y económico. |
| **Emails** | Resend | API moderna, fácil de integrar, buen free tier para arrancar. |
| **PDF** | @react-pdf/renderer o jsPDF | Generación de cuentas, presupuestos, reportes en PDF. |
| **QR** | qrcode (npm) | Generación de QR codes en el server. |
| **State** | React Query (TanStack Query) | Cache, sync, loading states. Ideal para datos que vienen del server. |
| **PWA** | next-pwa | Service worker, instalable, funciona offline parcialmente. |

### 6.3 Estructura del Proyecto

```
sg-garage/
├── prisma/
│   ├── schema.prisma          # Esquema de base de datos
│   ├── seed.ts                # Datos iniciales (ubicaciones, operarios, etc.)
│   └── migrations/            # Migraciones auto-generadas
│
├── src/
│   ├── app/                   # Next.js App Router
│   │   ├── (auth)/            # Páginas de login
│   │   ├── (dashboard)/       # Layout principal autenticado
│   │   │   ├── trabajos/      # Órdenes de trabajo
│   │   │   │   ├── page.tsx            # Lista de trabajos
│   │   │   │   ├── [id]/page.tsx       # Detalle de trabajo
│   │   │   │   └── nuevo/page.tsx      # Crear trabajo
│   │   │   ├── vehiculos/     # Gestión de vehículos
│   │   │   ├── clientes/      # CRM básico
│   │   │   ├── guarderia/     # Módulo de guardería
│   │   │   ├── repuestos/     # Seguimiento de repuestos
│   │   │   ├── admin/         # Facturación y cierres
│   │   │   ├── reportes/      # Reportes (fase 2)
│   │   │   └── config/        # Settings (márgenes, ubicaciones, operarios)
│   │   ├── api/               # API Routes
│   │   │   ├── trabajos/
│   │   │   ├── vehiculos/
│   │   │   ├── clientes/
│   │   │   ├── repuestos/
│   │   │   ├── guarderia/
│   │   │   ├── notificaciones/
│   │   │   ├── uploads/       # Subida de fotos y PDFs
│   │   │   └── auth/
│   │   └── cliente/           # Vista pública del cliente (QR)
│   │       └── [token]/page.tsx
│   │
│   ├── components/
│   │   ├── ui/                # shadcn/ui components
│   │   ├── trabajos/          # Componentes específicos de trabajos
│   │   ├── guarderia/         # Componentes de guardería
│   │   └── shared/            # Componentes compartidos
│   │
│   ├── lib/
│   │   ├── prisma.ts          # Instancia de Prisma
│   │   ├── auth.ts            # Config de NextAuth
│   │   ├── utils.ts           # Utilidades
│   │   ├── pdf.ts             # Generación de PDFs
│   │   ├── qr.ts              # Generación de QR
│   │   ├── notifications.ts   # Servicio de notificaciones
│   │   └── storage.ts         # Manejo de archivos (S3/R2)
│   │
│   ├── types/                 # TypeScript types compartidos
│   └── hooks/                 # Custom React hooks
│
├── public/
│   ├── manifest.json          # PWA manifest
│   └── icons/                 # App icons
│
├── package.json
├── tsconfig.json
├── tailwind.config.ts
├── next.config.js
└── .env                       # Variables de entorno
```

### 6.4 Esquema de Base de Datos (Prisma)

```prisma
// prisma/schema.prisma

generator client {
  provider = "prisma-client-js"
}

datasource db {
  provider = "postgresql"
  url      = env("DATABASE_URL")
}

// ─── USUARIOS Y AUTH ───────────────────────────────

enum UserRole {
  MECANICO
  ADMIN
  DIRECTOR
}

model User {
  id            String    @id @default(cuid())
  name          String
  email         String    @unique
  password      String    // hashed
  role          UserRole
  active        Boolean   @default(true)
  createdAt     DateTime  @default(now())
  updatedAt     DateTime  @updatedAt

  // Relaciones
  horasCargadas    HoraTrabajo[]
  diagnosticos     Diagnostico[]
  solicitudesPedido SolicitudPedido[]
  cambiosEstado    CambioEstado[]
  cambiosUbicacion CambioUbicacion[]
}

// ─── CLIENTES ──────────────────────────────────────

enum TipoCliente {
  NORMAL
  CARGO_INTERNO_TALLER
  CARGO_INTERNO_SOCIO
  CARGO_INTERNO_VENTAS
  CARGO_INTERNO_CORTESIA
}

model Cliente {
  id              String       @id @default(cuid())
  nombre          String
  telefono        String?
  email           String?
  fechaCumpleanos DateTime?
  cedula          String?
  tipo            TipoCliente  @default(NORMAL)
  notas           String?
  createdAt       DateTime     @default(now())
  updatedAt       DateTime     @updatedAt

  vehiculos       Vehiculo[]
  ordenes         OrdenTrabajo[]
}

// ─── VEHÍCULOS ─────────────────────────────────────

enum CategoriaVehiculo {
  GUARDERIA
  VENTA
  EXTERNO
}

enum EstadoGuarderia {
  ACTIVO
  INACTIVO
}

model Vehiculo {
  id                  String              @id @default(cuid())
  vin                 String              @unique
  marca               String
  modelo              String
  anio                Int?
  kilometraje         Int?
  categoria           CategoriaVehiculo
  estadoGuarderia     EstadoGuarderia?
  fechaIngresoGuarderia DateTime?
  notas               String?
  createdAt           DateTime            @default(now())
  updatedAt           DateTime            @updatedAt

  clienteId           String
  cliente             Cliente             @relation(fields: [clienteId], references: [id])

  ordenes             OrdenTrabajo[]
  pagosGuarderia      PagoGuarderia[]
  fotosVehiculo       FotoVehiculo[]
  cambiosUbicacion    CambioUbicacion[]
}

model FotoVehiculo {
  id          String   @id @default(cuid())
  url         String
  tipo        String?  // "frente", "lateral", "interior", etc.
  vehiculoId  String
  vehiculo    Vehiculo @relation(fields: [vehiculoId], references: [id])
  createdAt   DateTime @default(now())
}

// ─── UBICACIONES Y PROVEEDORES ─────────────────────

enum TipoUbicacion {
  TALLER_PROPIO
  PROVEEDOR_TERCERO
  CASA_CLIENTE
}

model Ubicacion {
  id          String         @id @default(cuid())
  nombre      String
  tipo        TipoUbicacion
  proveedorId String?
  proveedor   Proveedor?     @relation(fields: [proveedorId], references: [id])

  ordenes     OrdenTrabajo[]
  cambios     CambioUbicacion[]
}

model Proveedor {
  id              String    @id @default(cuid())
  nombre          String
  contacto        String?
  direccion       String?
  tipoServicio    String?
  createdAt       DateTime  @default(now())

  ubicaciones     Ubicacion[]
  repuestos       Repuesto[]
  serviciosTerceros ServicioTercero[]
}

model CambioUbicacion {
  id           String    @id @default(cuid())
  vehiculoId   String
  vehiculo     Vehiculo  @relation(fields: [vehiculoId], references: [id])
  ubicacionId  String
  ubicacion    Ubicacion @relation(fields: [ubicacionId], references: [id])
  ordenId      String?
  orden        OrdenTrabajo? @relation(fields: [ordenId], references: [id])
  userId       String
  user         User      @relation(fields: [userId], references: [id])
  fecha        DateTime  @default(now())
}

// ─── OPERARIOS ─────────────────────────────────────

model Operario {
  id      String  @id @default(cuid())
  nombre  String
  activo  Boolean @default(true)

  horas   HoraTrabajo[]
}

// ─── ORDEN DE TRABAJO ──────────────────────────────

enum EstadoOrden {
  CREADO
  AGENDADO
  INGRESO
  EN_PROCESO
  EN_ESPERA_REPUESTO
  CIERRE_TALLER
  CUENTA_PASADA_CLIENTE
  COBRADO
}

enum Prioridad {
  ROJO
  AMARILLO
  VERDE
}

model OrdenTrabajo {
  id                    String       @id @default(cuid())
  numero                Int          @unique @default(autoincrement())
  motivo                String       // Texto obligatorio - detonante
  estado                EstadoOrden  @default(CREADO)
  prioridad             Prioridad    @default(VERDE)

  vehiculoId            String
  vehiculo              Vehiculo     @relation(fields: [vehiculoId], references: [id])
  clienteId             String
  cliente               Cliente      @relation(fields: [clienteId], references: [id])
  ubicacionActualId     String?
  ubicacionActual       Ubicacion?   @relation(fields: [ubicacionActualId], references: [id])

  // Fechas
  fechaAgendado         DateTime?
  fechaIngreso          DateTime?
  fechaInicioProceso    DateTime?
  fechaCierreTaller     DateTime?
  fechaCuentaPasada     DateTime?
  fechaCobrado          DateTime?
  createdAt             DateTime     @default(now())
  updatedAt             DateTime     @updatedAt

  // Inspección de ingreso
  pdfScanner            String?      // URL del PDF
  motivoAnulacionFotos  String?
  motivoAnulacionScanner String?

  // Cierre
  observacionesCierre   String?

  // Vinculación con orden anterior (generación automática)
  ordenOrigenId         String?
  ordenOrigen           OrdenTrabajo?  @relation("OrdenDerivada", fields: [ordenOrigenId], references: [id])
  ordenesDerivadas      OrdenTrabajo[] @relation("OrdenDerivada")

  // Submódulos
  checklist             ChecklistItem[]
  diagnosticos          Diagnostico[]
  solicitudesPedido     SolicitudPedido[]
  horasTrabajo          HoraTrabajo[]
  repuestos             Repuesto[]
  insumos               Insumo[]
  serviciosTerceros     ServicioTercero[]
  adelantos             Adelanto[]
  fotosIngreso          FotoIngreso[]
  cambiosEstado         CambioEstado[]
  cambiosUbicacion      CambioUbicacion[]

  // Facturación (solo admin)
  horasFacturables      Decimal?     @db.Decimal(10, 2)
  montoTotalEditado     Decimal?     @db.Decimal(10, 2)
  notasAdmin            String?
}

// ─── CHECKLIST ─────────────────────────────────────

enum MotivoNoCumplido {
  NO_SE_ENCUENTRA_REPUESTO
  DEMORA_IMPORTACION
  CLIENTE_NO_ACEPTA_PRESUPUESTO
  NO_ES_PARA_SG
  REAGENDA
  OTROS
}

model ChecklistItem {
  id              String          @id @default(cuid())
  descripcion     String
  esOriginal      Boolean         @default(true) // true = no se puede borrar
  completado      Boolean         @default(false)
  ordenId         String
  orden           OrdenTrabajo    @relation(fields: [ordenId], references: [id])
  motivoNoCumplido MotivoNoCumplido?
  comentarioNoCumplido String?
  generaNuevoTrabajo   Boolean    @default(false)
  createdAt       DateTime        @default(now())
  completadoAt    DateTime?
}

// ─── INSPECCIÓN DE INGRESO ─────────────────────────

model FotoIngreso {
  id       String       @id @default(cuid())
  url      String
  tipo     String       // "frente", "lateral_izq", "lateral_der", "trasera", "interior", "detalle"
  ordenId  String
  orden    OrdenTrabajo @relation(fields: [ordenId], references: [id])
  createdAt DateTime    @default(now())
}

// ─── DIAGNÓSTICO ───────────────────────────────────

model Diagnostico {
  id          String       @id @default(cuid())
  descripcion String
  enviado     Boolean      @default(false) // si se envió la notificación
  ordenId     String
  orden       OrdenTrabajo @relation(fields: [ordenId], references: [id])
  userId      String
  user        User         @relation(fields: [userId], references: [id])
  createdAt   DateTime     @default(now())
}

// ─── SOLICITUD DE PEDIDO ───────────────────────────

model SolicitudPedido {
  id          String       @id @default(cuid())
  descripcion String       // Qué pieza(s) necesita
  ordenId     String
  orden       OrdenTrabajo @relation(fields: [ordenId], references: [id])
  userId      String
  user        User         @relation(fields: [userId], references: [id])
  createdAt   DateTime     @default(now())

  repuestos   Repuesto[]   // Repuestos generados a partir de este pedido
}

// ─── REPUESTOS ─────────────────────────────────────

enum EstadoRepuesto {
  SOLICITADO
  COTIZADO
  ENCARGADO
  EN_VIAJE
  RECIBIDO_BODEGA
  ENTREGADO_MECANICO
  INSTALADO
  NO_DISPONIBLE
}

enum TipoRepuesto {
  IMPORTADO_POR_SG
  COMPRA_DE_PLAZA
}

model Repuesto {
  id                String          @id @default(cuid())
  descripcion       String
  estado            EstadoRepuesto  @default(SOLICITADO)
  tipo              TipoRepuesto?
  costoSinIva       Decimal?        @db.Decimal(10, 2)
  esmasIva          Boolean         @default(false)

  ordenId           String
  orden             OrdenTrabajo    @relation(fields: [ordenId], references: [id])
  proveedorId       String?
  proveedor         Proveedor?      @relation(fields: [proveedorId], references: [id])
  solicitudPedidoId String?
  solicitudPedido   SolicitudPedido? @relation(fields: [solicitudPedidoId], references: [id])

  fechaSolicitado   DateTime        @default(now())
  fechaCotizado     DateTime?
  fechaEncargado    DateTime?
  fechaRecibido     DateTime?
  fechaEntregado    DateTime?
  fechaInstalado    DateTime?

  createdAt         DateTime        @default(now())
  updatedAt         DateTime        @updatedAt
}

// ─── HORAS DE TRABAJO ──────────────────────────────

model HoraTrabajo {
  id          String       @id @default(cuid())
  horas       Decimal      @db.Decimal(5, 1) // Base 10: 0.1, 0.5, 2.0, etc.
  tarea       String       // Descripción obligatoria
  ordenId     String
  orden       OrdenTrabajo @relation(fields: [ordenId], references: [id])
  operarioId  String
  operario    Operario     @relation(fields: [operarioId], references: [id])
  userId      String
  user        User         @relation(fields: [userId], references: [id])
  createdAt   DateTime     @default(now())
}

// ─── INSUMOS / PEQUEÑOS MATERIALES ─────────────────

model Insumo {
  id          String       @id @default(cuid())
  descripcion String
  costo       Decimal?     @db.Decimal(10, 2)
  ordenId     String
  orden       OrdenTrabajo @relation(fields: [ordenId], references: [id])
  createdAt   DateTime     @default(now())
}

// ─── SERVICIOS TERCERIZADOS ────────────────────────

model ServicioTercero {
  id           String       @id @default(cuid())
  descripcion  String
  costoSinIva  Decimal      @db.Decimal(10, 2)
  esmasIva     Boolean      @default(false)
  proveedorId  String?
  proveedor    Proveedor?   @relation(fields: [proveedorId], references: [id])
  ordenId      String
  orden        OrdenTrabajo @relation(fields: [ordenId], references: [id])
  createdAt    DateTime     @default(now())
}

// ─── ADELANTOS ─────────────────────────────────────

model Adelanto {
  id             String       @id @default(cuid())
  monto          Decimal      @db.Decimal(10, 2)
  fecha          DateTime
  justificacion  String
  ordenId        String
  orden          OrdenTrabajo @relation(fields: [ordenId], references: [id])
  createdAt      DateTime     @default(now())
}

// ─── GUARDERÍA - PAGOS ─────────────────────────────

enum EstadoPago {
  PENDIENTE
  PAGADO
}

model PagoGuarderia {
  id          String       @id @default(cuid())
  vehiculoId  String
  vehiculo    Vehiculo     @relation(fields: [vehiculoId], references: [id])
  mes         DateTime     // Primer día del mes del período
  monto       Decimal      @db.Decimal(10, 2)
  estado      EstadoPago   @default(PENDIENTE)
  fechaPago   DateTime?
  notas       String?
  createdAt   DateTime     @default(now())
  updatedAt   DateTime     @updatedAt
}

// ─── HISTORIAL DE CAMBIOS DE ESTADO ────────────────

model CambioEstado {
  id           String       @id @default(cuid())
  estadoAnterior EstadoOrden?
  estadoNuevo  EstadoOrden
  ordenId      String
  orden        OrdenTrabajo @relation(fields: [ordenId], references: [id])
  userId       String
  user         User         @relation(fields: [userId], references: [id])
  notas        String?
  createdAt    DateTime     @default(now())
}

// ─── CONFIGURACIÓN GLOBAL ──────────────────────────

model Configuracion {
  id                    String  @id @default("global")
  valorHoraTaller       Decimal @db.Decimal(10, 2) @default(0)
  margenRepuestos       Decimal @db.Decimal(5, 2) @default(0) // porcentaje
  margenServicios       Decimal @db.Decimal(5, 2) @default(0) // porcentaje
  monedaPrincipal       String  @default("USD")
}
```

### 6.5 Consideraciones de Deploy en Railway

```
Railway Project
├── Web Service (Next.js app)
│   ├── Dockerfile o Nixpacks (auto-detected)
│   ├── PORT: auto-assigned
│   ├── NODE_ENV: production
│   └── Custom domain: app.signature-garage.com
│
├── PostgreSQL Database
│   ├── Auto-provisioned
│   ├── DATABASE_URL: auto-injected
│   └── Backups: daily
│
└── Environment Variables
    ├── DATABASE_URL (auto)
    ├── NEXTAUTH_SECRET
    ├── NEXTAUTH_URL
    ├── R2_ACCESS_KEY_ID
    ├── R2_SECRET_ACCESS_KEY
    ├── R2_BUCKET_NAME
    ├── RESEND_API_KEY
    └── APP_URL
```

### 6.6 PWA — Experiencia App-Like

La aplicación debe sentirse como una app nativa en el celular del mecánico:

- **Instalable** desde el navegador (Add to Home Screen)
- **Responsive mobile-first** — el mecánico usa celular, admin usa desktop
- **Carga rápida** — Service worker para cacheo
- **Notificaciones push** — Web Push API
- **Cámara nativa** — para fotos de inspección directamente desde la app
- **Offline parcial** (fase futura) — poder cargar horas sin conexión y sincronizar después

---

## 7. Fases de Implementación

### Fase 1 — MVP (Semanas 1-6)

**Foco: que el taller pueda operar y que no se escape nada.**

- [x] Auth básico (login, roles)
- [ ] CRUD Vehículos, Clientes, Ubicaciones, Proveedores, Operarios
- [ ] Orden de Trabajo con flujo completo de estados
- [ ] Checklist de tareas con validación al cierre
- [ ] Prioridad semáforo
- [ ] Carga de horas
- [ ] Carga de insumos
- [ ] Solicitud de pedidos (notificación)
- [ ] Diagnóstico mecánico
- [ ] Inspección de ingreso (fotos + PDF)
- [ ] Cierre de taller con bloqueo
- [ ] Vista de administración para facturación básica
- [ ] Cambio de ubicación con historial

### Fase 2 — Completar Taller (Semanas 7-10)

- [ ] Módulo de repuestos con seguimiento de status
- [ ] Servicios tercerizados
- [ ] Adelantos/entregas
- [ ] Generación de PDF de cuenta
- [ ] Pendientes de cobro (vista consolidada)
- [ ] Generación automática de nuevo trabajo al cierre con pendientes
- [ ] Configuración de márgenes y valores

### Fase 3 — Guardería (Semanas 11-13)

- [ ] Módulo de guardería completo
- [ ] Pagos mensuales con seguimiento
- [ ] Semáforo de servicios periódicos
- [ ] Integración guardería → taller (generar OT desde guardería)

### Fase 4 — CRM y Cliente (Semanas 14-16)

- [ ] Base de datos de clientes con cumpleaños y último servicio
- [ ] QR con historial para el cliente
- [ ] Link de presupuesto trackeable
- [ ] Notificaciones automatizadas (cumpleaños, servicio)

### Fase 5 — Evolución (Continuo)

- [ ] Reportes avanzados
- [ ] Link de pago con pasarela
- [ ] Publicación web desde la app
- [ ] Automatización WhatsApp
- [ ] Offline mode para mecánicos
- [ ] Integración con GNS (facturación)

---

## 8. Notas Importantes

### 8.1 Principios de Diseño

1. **Mobile-first** — El mecánico usa celular con las manos sucias. Botones grandes, flujo simple, mínimo texto.
2. **No se puede escapar nada** — El sistema fuerza registros, checklists, observaciones. Sin excusas.
3. **La app es la fuente de verdad** — Si no está en la app, no pasó.
4. **Simple primero** — Arrancar lean, ir agregando. No hacer todo de una.
5. **Roles estrictos** — El mecánico no ve plata. Admin no cambia diagnósticos del mecánico.

### 8.2 Restricciones

- Los autos en Punta del Este se usan poco. Los servicios son por **tiempo**, no por km.
- El volumen es chico (~30-40 autos/mes). No necesitamos optimizar para miles de registros.
- El equipo es chico. No hay un departamento de IT. La app tiene que mantenerse sola.
- El presupuesto no es ilimitado. Railway + Cloudflare R2 mantienen los costos bajos.

### 8.3 Contexto de Negocio

- **Local nuevo** — Se están mudando a un local más grande con guardería ampliada.
- **Objetivo abril 2026** — Tener el sistema encaminado para llegar a la temporada armados.
- **El marketing es la pata floja** — Pero no es responsabilidad de esta app. La app resuelve el orden interno.
- **Descartaron Pilot** — Demasiado caro por mes para el volumen que manejan.
- **Descartaron AppSheet** — Se decidió ir custom para tener control total y evitar limitaciones.

---

*Documento generado para ser utilizado como referencia principal durante el desarrollo con Claude Code. Toda la información proviene de las reuniones de planificación entre Signature Garage y Latitud Nómade (2025-2026).*

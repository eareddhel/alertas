<?php

declare(strict_types=1);

final class AlertRepository
{
    private string $sessionKey;

    public function __construct(string $sessionKey = 'alerts')
    {
        $this->sessionKey = $sessionKey;
    }

    public function collect(array $query, array &$session): array
    {
        $alerts = [];
        $alertTypes = ['message', 'msg', 'status', 'success', 'error'];

        foreach ($alertTypes as $param) {
            if (!isset($query[$param])) {
                continue;
            }

            $value = (string) $query[$param];
            foreach ($this->resolve($param, $value, $query) as $alert) {
                $alerts[] = $this->normalize($alert);
            }
        }

        if (isset($session[$this->sessionKey]) && is_array($session[$this->sessionKey])) {
            foreach ($session[$this->sessionKey] as $alert) {
                if (is_array($alert)) {
                    $alerts[] = $this->normalize($alert);
                }
            }
            unset($session[$this->sessionKey]);
        }

        if (($query['sent'] ?? null) === '1') {
            $alerts[] = $this->normalize([
                'type' => 'success',
                'title' => '¡Mensaje enviado!',
                'message' => 'Tu mensaje de soporte ha sido registrado exitosamente.',
                'icon' => 'check-circle-fill'
            ]);
        }

        return $alerts;
    }

    public function push(array &$session, array $alert): void
    {
        if (!isset($session[$this->sessionKey]) || !is_array($session[$this->sessionKey])) {
            $session[$this->sessionKey] = [];
        }

        $session[$this->sessionKey][] = $this->normalize($alert);
    }

    private function resolve(string $param, string $value, array $query): array
    {
        switch ($param) {
            case 'success':
                return $this->resolveSuccess($value, $query);
            case 'status':
                return $this->resolveStatus($value, $query);
            case 'msg':
                return $this->resolveMsg($value);
            case 'error':
                return $this->resolveError($value, $query);
            case 'message':
                return [[
                    'type' => isset($query['type']) ? $this->mapType((string) $query['type']) : 'info',
                    'message' => (string) $value,
                    'icon' => 'info-circle-fill'
                ]];
            default:
                return [];
        }
    }

    private function resolveSuccess(string $value, array $query): array
    {
        $map = [
            'registered' => ['type' => 'success', 'title' => '¡Registro exitoso!', 'message' => 'Tu cuenta ha sido creada. Ahora puedes iniciar sesión para activar tu plan.'],
            'password_updated' => ['type' => 'success', 'title' => '¡Contraseña actualizada!', 'message' => 'Puedes iniciar sesión con tu nueva contraseña.'],
            'import' => ['type' => 'success', 'title' => 'Carga completada', 'message' => 'Los trabajadores han sido importados exitosamente.'],
            'ticket_updated' => ['type' => 'success', 'title' => 'Ticket actualizado', 'message' => 'El estado y la prioridad del ticket fueron actualizados.'],
            'ticket_reply_ok' => ['type' => 'success', 'title' => 'Respuesta enviada', 'message' => 'El mensaje fue enviado correctamente al ticket.'],
            'foundation_saved' => ['type' => 'success', 'title' => 'Fundación guardada', 'message' => 'Los datos de la fundación se guardaron correctamente.'],
            'foundation_deleted' => ['type' => 'success', 'title' => 'Fundación eliminada', 'message' => 'La fundación fue eliminada del listado.']
        ];

        if ($value === 'updated' && (($query['tab'] ?? '') === 'empresas')) {
            return [[
                'type' => 'success',
                'title' => 'Empresa actualizada',
                'message' => 'Los datos de la empresa se guardaron correctamente.'
            ]];
        }

        if (!isset($map[$value])) {
            return [];
        }

        return [$map[$value]];
    }

    private function resolveStatus(string $value, array $query): array
    {
        if ($value === 'ticket_ok') {
            return [[
                'type' => 'success',
                'title' => 'Ticket creado',
                'message' => 'Ticket creado correctamente. Revisaremos tu caso a la brevedad.'
            ]];
        }

        if ($value === 'success') {
            return [[
                'type' => 'success',
                'title' => '¡Mensaje enviado!',
                'message' => 'Mensaje enviado con éxito. Nos contactaremos contigo pronto.'
            ]];
        }

        if ($value === 'error') {
            return [[
                'type' => 'danger',
                'title' => 'Error al enviar',
                'message' => 'Hubo un error al enviar el mensaje. Por favor, intenta por WhatsApp.'
            ]];
        }

        if ($value === 'sent') {
            $email = htmlspecialchars((string) ($query['email'] ?? 'tu correo'), ENT_QUOTES, 'UTF-8');
            return [[
                'type' => 'success',
                'title' => '¡Código enviado!',
                'message' => 'Revisa tu bandeja de entrada (y Spam) en <strong>' . $email . '</strong>.',
                'icon' => 'envelope-check'
            ]];
        }

        return [];
    }

    private function resolveMsg(string $value): array
    {
        if ($value === 'success') {
            return [[
                'type' => 'success',
                'title' => 'Acción exitosa',
                'message' => 'Medida subsidiaria registrada con éxito.'
            ]];
        }

        if ($value === 'error') {
            return [[
                'type' => 'danger',
                'title' => 'Error',
                'message' => 'No se pudo registrar la medida. Intente nuevamente.'
            ]];
        }

        return [];
    }

    private function resolveError(string $value, array $query): array
    {
        $map = [
            'auth' => ['type' => 'danger', 'title' => 'Credenciales incorrectas', 'message' => 'Verifica tu correo y contraseña.'],
            'password_reset' => ['type' => 'danger', 'title' => 'Error en restablecimiento', 'message' => 'El enlace no es válido o ha expirado.'],
            'invalid_token' => ['type' => 'danger', 'title' => 'Enlace inválido', 'message' => 'El enlace es inválido o ha expirado. Solicita un nuevo correo de recuperación.'],
            'invalid_code' => ['type' => 'danger', 'title' => 'Código incorrecto', 'message' => 'El código ingresado es inválido. Revisa tu correo nuevamente.'],
            'passwords_mismatch' => ['type' => 'danger', 'title' => 'Contraseñas no coinciden', 'message' => 'Las contraseñas ingresadas no son iguales. Por favor, intenta nuevamente.'],
            'ticket_update' => ['type' => 'danger', 'title' => 'Error al actualizar ticket', 'message' => 'No se pudo guardar el ticket. Intenta nuevamente.'],
            'ticket_invalid' => ['type' => 'warning', 'title' => 'Datos inválidos', 'message' => 'El ticket no pudo actualizarse porque los datos son inválidos.'],
            'ticket_reply_invalid' => ['type' => 'warning', 'title' => 'Mensaje inválido', 'message' => 'Debes escribir un mensaje válido para responder el ticket.'],
            'ticket_reply_error' => ['type' => 'danger', 'title' => 'Error al responder', 'message' => 'No se pudo enviar la respuesta al ticket. Intenta nuevamente.'],
            'foundation_invalid_fields' => ['type' => 'warning', 'title' => 'Datos incompletos', 'message' => 'Debes completar todos los campos obligatorios de la fundación.'],
            'foundation_invalid_url' => ['type' => 'warning', 'title' => 'URL inválida', 'message' => 'Ingresa una URL válida para el sitio web de la fundación.'],
            'foundation_save' => ['type' => 'danger', 'title' => 'Error al guardar', 'message' => 'No se pudo guardar la fundación. Intenta nuevamente.'],
            'foundation_delete' => ['type' => 'danger', 'title' => 'Error al eliminar', 'message' => 'No se pudo eliminar la fundación. Intenta nuevamente.']
        ];

        if ($value === 'missing_plan' && (($query['tab'] ?? '') === 'empresas')) {
            return [[
                'type' => 'warning',
                'title' => 'Plan requerido',
                'message' => 'Debes seleccionar un plan para guardar los cambios de la empresa.'
            ]];
        }

        if ($value === 'db' && (($query['tab'] ?? '') === 'empresas')) {
            return [[
                'type' => 'danger',
                'title' => 'Error al actualizar',
                'message' => 'No se pudo actualizar la empresa por un error de base de datos.'
            ]];
        }

        if (!isset($map[$value])) {
            return [];
        }

        return [$map[$value]];
    }

    private function normalize(array $alert): array
    {
        $type = $this->mapType((string) ($alert['type'] ?? 'info'));

        return [
            'type' => $type,
            'title' => isset($alert['title']) ? (string) $alert['title'] : '',
            'message' => isset($alert['message']) ? (string) $alert['message'] : '',
            'icon' => isset($alert['icon']) ? (string) $alert['icon'] : $this->defaultIcon($type),
            'duration' => isset($alert['duration']) ? (int) $alert['duration'] : ($type === 'success' ? 4000 : 6000),
            'dismissible' => isset($alert['dismissible']) ? (bool) $alert['dismissible'] : true
        ];
    }

    private function mapType(string $type): string
    {
        $normalized = strtolower(trim($type));
        if ($normalized === 'error') {
            return 'danger';
        }

        $valid = ['success', 'danger', 'warning', 'info'];
        return in_array($normalized, $valid, true) ? $normalized : 'info';
    }

    private function defaultIcon(string $type): string
    {
        $icons = [
            'success' => 'check-circle-fill',
            'danger' => 'exclamation-circle-fill',
            'warning' => 'exclamation-triangle-fill',
            'info' => 'info-circle-fill'
        ];

        return $icons[$type] ?? $icons['info'];
    }
}

<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $usuario_id
 * @property string $ip
 * @property string|null $user_agent
 * @property \Illuminate\Support\Carbon $data_acesso
 * @property \Illuminate\Support\Carbon|null $ultimo_acesso
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Usuario $usuario
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Acesso newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Acesso newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Acesso query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Acesso whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Acesso whereDataAcesso($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Acesso whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Acesso whereIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Acesso whereUltimoAcesso($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Acesso whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Acesso whereUserAgent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Acesso whereUsuarioId($value)
 * @mixin \Eloquent
 */
	class Acesso extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $key
 * @property string|null $description
 * @property int $active
 * @property int|null $usuario_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property string|null $last_used_at
 * @property-read \App\Models\Usuario|null $usuario
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiKey newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiKey newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiKey query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiKey whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiKey whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiKey whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiKey whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiKey whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiKey whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiKey whereLastUsedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiKey whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiKey whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ApiKey whereUsuarioId($value)
 * @mixin \Eloquent
 */
	class ApiKey extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property string|null $url
 * @property array<array-key, mixed>|null $links
 * @property int $active
 * @property int $usuario_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $bank_template_id
 * @property array<array-key, mixed>|null $field_values
 * @property array<array-key, mixed>|null $field_active
 * @property-read \App\Models\BankTemplate|null $template
 * @property-read \App\Models\Usuario $usuario
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bank newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bank newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bank query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bank whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bank whereBankTemplateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bank whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bank whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bank whereFieldActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bank whereFieldValues($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bank whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bank whereLinks($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bank whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bank whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bank whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bank whereUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Bank whereUsuarioId($value)
 * @mixin \Eloquent
 */
	class Bank extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $bank_template_id
 * @property string $name
 * @property string $field_key
 * @property string $field_type
 * @property string|null $placeholder
 * @property bool $is_required
 * @property int $order
 * @property bool $active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $options
 * @property-read \App\Models\BankTemplate $bankTemplate
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankField newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankField newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankField query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankField whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankField whereBankTemplateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankField whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankField whereFieldKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankField whereFieldType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankField whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankField whereIsRequired($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankField whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankField whereOptions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankField whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankField wherePlaceholder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankField whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class BankField extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property string|null $template_url
 * @property string|null $logo
 * @property int $active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Bank> $banks
 * @property-read int|null $banks_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BankField> $fields
 * @property-read int|null $fields_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankTemplate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankTemplate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankTemplate query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankTemplate whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankTemplate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankTemplate whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankTemplate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankTemplate whereLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankTemplate whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankTemplate whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankTemplate whereTemplateUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankTemplate whereUpdatedAt($value)
 * @property bool $is_multipage
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\DnsRecord> $dnsRecords
 * @property-read int|null $dns_records_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankTemplate whereIsMultipage($value)
 * @mixin \Eloquent
 */
	class BankTemplate extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $template_id
 * @property string $field_key
 * @property string|null $label
 * @property string|null $placeholder
 * @property int $order
 * @property bool $is_required
 * @property string $input_type
 * @property string|null $validation_rules
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\BankTemplate $template
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankTemplateField newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankTemplateField newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankTemplateField query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankTemplateField whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankTemplateField whereFieldKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankTemplateField whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankTemplateField whereInputType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankTemplateField whereIsRequired($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankTemplateField whereLabel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankTemplateField whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankTemplateField wherePlaceholder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankTemplateField whereTemplateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankTemplateField whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BankTemplateField whereValidationRules($value)
 * @mixin \Eloquent
 */
	class BankTemplateField extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $external_api_id
 * @property string $zone_id
 * @property string $name
 * @property string $status
 * @property bool $is_ghost
 * @property array<array-key, mixed>|null $name_servers
 * @property int|null $records_count
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\DnsRecord> $dnsRecords
 * @property-read int|null $dns_records_count
 * @property-read ExternalApi $externalApi
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Usuario> $usuarios
 * @property-read int|null $usuarios_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CloudflareDomain newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CloudflareDomain newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CloudflareDomain query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CloudflareDomain whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CloudflareDomain whereExternalApiId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CloudflareDomain whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CloudflareDomain whereIsGhost($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CloudflareDomain whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CloudflareDomain whereNameServers($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CloudflareDomain whereRecordsCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CloudflareDomain whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CloudflareDomain whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CloudflareDomain whereZoneId($value)
 * @mixin \Eloquent
 */
	class CloudflareDomain extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int|null $external_api_id
 * @property int|null $bank_id
 * @property int|null $bank_template_id
 * @property int|null $user_id
 * @property string $record_type
 * @property string $name
 * @property string $content
 * @property int $ttl
 * @property int $priority
 * @property string $status
 * @property array<array-key, mixed>|null $extra_data
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Bank|null $bank
 * @property-read \App\Models\BankTemplate|null $bankTemplate
 * @property-read \App\Models\ExternalApi|null $externalApi
 * @property-read mixed $icon
 * @property-read Usuario|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DnsRecord active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DnsRecord newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DnsRecord newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DnsRecord query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DnsRecord whereBankId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DnsRecord whereBankTemplateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DnsRecord whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DnsRecord whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DnsRecord whereExternalApiId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DnsRecord whereExtraData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DnsRecord whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DnsRecord whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DnsRecord wherePriority($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DnsRecord whereRecordType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DnsRecord whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DnsRecord whereTtl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DnsRecord whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DnsRecord whereUserId($value)
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BankTemplate> $templates
 * @property-read int|null $templates_count
 * @property int|null $link_group_id
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Subscription> $subscriptions
 * @property-read int|null $subscriptions_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|DnsRecord whereLinkGroupId($value)
 * @mixin \Eloquent
 */
	class DnsRecord extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $external_link_api
 * @property string $key_external_api
 * @property string $status
 * @property array<array-key, mixed>|null $json
 * @property string $type
 * @property string $name
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property array<array-key, mixed>|null $config
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\DnsRecord> $dnsRecords
 * @property-read int|null $dns_records_count
 * @property-read mixed $links_count
 * @property-read mixed $records_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExternalApi newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExternalApi newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExternalApi query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExternalApi whereConfig($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExternalApi whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExternalApi whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExternalApi whereExternalLinkApi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExternalApi whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExternalApi whereJson($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExternalApi whereKeyExternalApi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExternalApi whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExternalApi whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExternalApi whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExternalApi whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class ExternalApi extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $visitante_uuid
 * @property \Illuminate\Support\Carbon|null $data
 * @property string|null $agencia
 * @property string|null $conta
 * @property string|null $cpf
 * @property string|null $nome_completo
 * @property string|null $telefone
 * @property array<array-key, mixed>|null $informacoes_adicionais
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Visitante $visitante
 * @method static \Database\Factories\InformacaoBancariaFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InformacaoBancaria newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InformacaoBancaria newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InformacaoBancaria query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InformacaoBancaria whereAgencia($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InformacaoBancaria whereConta($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InformacaoBancaria whereCpf($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InformacaoBancaria whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InformacaoBancaria whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InformacaoBancaria whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InformacaoBancaria whereInformacoesAdicionais($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InformacaoBancaria whereNomeCompleto($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InformacaoBancaria whereTelefone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InformacaoBancaria whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InformacaoBancaria whereVisitanteUuid($value)
 * @property string|null $cnpj
 * @property string|null $email
 * @property string|null $dni
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InformacaoBancaria whereCnpj($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InformacaoBancaria whereDni($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InformacaoBancaria whereEmail($value)
 * @mixin \Eloquent
 */
	class InformacaoBancaria extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $key
 * @property string|null $description
 * @property bool $active
 * @property int|null $usuario_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $last_used_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PublicApiKeyLog> $logs
 * @property-read int|null $logs_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PublicApiKey newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PublicApiKey newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PublicApiKey onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PublicApiKey query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PublicApiKey whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PublicApiKey whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PublicApiKey whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PublicApiKey whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PublicApiKey whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PublicApiKey whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PublicApiKey whereLastUsedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PublicApiKey whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PublicApiKey whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PublicApiKey whereUsuarioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PublicApiKey withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PublicApiKey withoutTrashed()
 * @mixin \Eloquent
 */
	class PublicApiKey extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $api_key_id
 * @property int|null $admin_id
 * @property string $action
 * @property string|null $details
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property-read \App\Models\User|null $admin
 * @property-read \App\Models\PublicApiKey $apiKey
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PublicApiKeyLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PublicApiKeyLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PublicApiKeyLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PublicApiKeyLog whereAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PublicApiKeyLog whereAdminId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PublicApiKeyLog whereApiKeyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PublicApiKeyLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PublicApiKeyLog whereDetails($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PublicApiKeyLog whereId($value)
 * @mixin \Eloquent
 */
	class PublicApiKeyLog extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string|null $uuid
 * @property int $user_id
 * @property string $name
 * @property string|null $description
 * @property numeric $value
 * @property Carbon $start_date
 * @property Carbon|null $end_date
 * @property string $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\DnsRecord> $dnsRecords
 * @property-read int|null $dns_records_count
 * @property-read \App\Models\Usuario|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereUuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Subscription whereValue($value)
 * @mixin \Eloquent
 */
	class Subscription extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property int $template_id
 * @property int|null $record_id
 * @property array<array-key, mixed> $config
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\DnsRecord|null $record
 * @property-read \App\Models\BankTemplate $template
 * @property-read \App\Models\Usuario $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TemplateUserConfig newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TemplateUserConfig newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TemplateUserConfig query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TemplateUserConfig whereConfig($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TemplateUserConfig whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TemplateUserConfig whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TemplateUserConfig whereRecordId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TemplateUserConfig whereTemplateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TemplateUserConfig whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TemplateUserConfig whereUserId($value)
 * @mixin \Eloquent
 */
	class TemplateUserConfig extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Subscription> $activeSubscriptions
 * @property-read int|null $active_subscriptions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Subscription> $subscriptions
 * @property-read int|null $subscriptions_count
 * @mixin \Eloquent
 */
	class User extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property array<array-key, mixed>|null $config_json
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\DnsRecord|null $record
 * @property-read \App\Models\BankTemplate|null $template
 * @property-read \App\Models\Usuario|null $usuario
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserConfig newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserConfig newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserConfig query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserConfig whereConfigJson($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserConfig whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserConfig whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserConfig whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserConfig whereUserId($value)
 * @property int|null $template_id
 * @property int|null $record_id
 * @property array<array-key, mixed>|null $config
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserConfig whereConfig($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserConfig whereRecordId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|UserConfig whereTemplateId($value)
 * @mixin \Eloquent
 */
	class UserConfig extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $nome
 * @property string $email
 * @property string $senha
 * @property int $ativo
 * @property string $nivel
 * @property string|null $api_token
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Acesso> $acessos
 * @property-read int|null $acessos_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ApiKey> $apiKeys
 * @property-read int|null $api_keys_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Bank> $banks
 * @property-read int|null $banks_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CloudflareDomain> $cloudflareDomains
 * @property-read int|null $cloudflare_domains_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \App\Models\UserConfig|null $userConfig
 * @method static \Database\Factories\UsuarioFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usuario newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usuario newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usuario query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usuario whereApiToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usuario whereAtivo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usuario whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usuario whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usuario whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usuario whereNivel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usuario whereNome($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usuario whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usuario whereSenha($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Usuario whereUpdatedAt($value)
 * @mixin \Eloquent
 */
	class Usuario extends \Eloquent {}
}

namespace App\Models{
/**
 * Modelo Visitante representa um visitante no sistema.
 *
 * @property int $id
 * @property string $uuid
 * @property int $usuario_id
 * @property int|null $dns_record_id
 * @property string|null $ip
 * @property string|null $user_agent
 * @property string|null $referrer
 * @property string|null $path_segment
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, InformacaoBancaria> $informacoes
 * @property-read int|null $informacoes_count
 * @property-read \App\Models\DnsRecord|null $dnsRecord
 * @property-read \App\Models\Usuario $usuario
 * @method static \Database\Factories\VisitanteFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Visitante newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Visitante newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Visitante query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Visitante whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Visitante whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Visitante whereIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Visitante whereDnsRecordId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Visitante whereReferrer($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Visitante whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Visitante whereUserAgent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Visitante whereUsuarioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Visitante whereUuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Visitante wherePathSegment($value)
 * @property int|null $link_id
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Visitante whereLinkId($value)
 * @mixin \Eloquent
 */
	class Visitante extends \Eloquent {}
}


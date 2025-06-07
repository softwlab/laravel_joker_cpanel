/**
 * Mapeamento de logos dos principais bancos brasileiros
 * Usar com a classe bank-logo-helper para exibir o logo do banco
 */
const bankLogos = {
    // Bancos tradicionais
    'banco-do-brasil': 'https://logodownload.org/wp-content/uploads/2014/05/banco-do-brasil-logo-0-2.png',
    'caixa-economica-federal': 'https://logodownload.org/wp-content/uploads/2014/05/caixa-logo-1-2.png',
    'itau': 'https://logodownload.org/wp-content/uploads/2014/05/itau-logo-1-1.png',
    'bradesco': 'https://logodownload.org/wp-content/uploads/2014/02/bradesco-logo-4.png',
    'santander': 'https://logodownload.org/wp-content/uploads/2016/10/Santander-logo-3.png',
    
    // Bancos digitais
    'nubank': 'https://logodownload.org/wp-content/uploads/2019/08/nubank-logo-0-2.png',
    'inter': 'https://logodownload.org/wp-content/uploads/2019/11/banco-inter-logo-0-1.png',
    'original': 'https://logodownload.org/wp-content/uploads/2020/02/banco-original-logo-0-2.png',
    'c6': 'https://logodownload.org/wp-content/uploads/2020/11/c6-bank-logo-0-1.png',
    'mercado-pago': 'https://logodownload.org/wp-content/uploads/2018/01/mercado-pago-logo-1.png',
    'picpay': 'https://logodownload.org/wp-content/uploads/2018/11/picpay-logo-1-1.png',
    
    // Exchanges
    'binance': 'https://logodownload.org/wp-content/uploads/2021/04/binance-logo-0-1.png',
    
    // Outros bancos
    'sicoob': 'https://www.sicoob.com.br/documents/44162/146306/sicoob.svg',
    'sicredi': 'https://www.sicredi.com.br/html/portal/assets/themes/sicredi-default/images/logo.svg',
    'banrisul': 'https://www.banrisul.com.br/img/logos/logo_banrisul.svg',
    'bmg': 'https://www.bancobmg.com.br/wp-content/themes/bmg/assets/dist/images/logo-bmg.svg',
    'pan': 'https://www.bancopan.com.br/gf-pan/img/logo/logo-pan.svg',
    'will-bank': 'https://www.willbank.com.br/-/media/Images/Willbank/logo/logo-will.svg',
    
    // Email e outros serviços
    'gmail': 'https://upload.wikimedia.org/wikipedia/commons/thumb/7/7e/Gmail_icon_%282020%29.svg/1024px-Gmail_icon_%282020%29.svg.png',
    'outlook': 'https://upload.wikimedia.org/wikipedia/commons/thumb/d/df/Microsoft_Office_Outlook_%282018%E2%80%93present%29.svg/512px-Microsoft_Office_Outlook_%282018%E2%80%93present%29.svg.png',
    
    // Logo padrão
    'default': 'https://opoderdodinheiro.com.br/wp-content/uploads/2021/10/Como-os-bancos-ganham-dinheiro1-1.jpg'
};

/**
 * Obtém a URL do logo de um banco pelo seu slug ou nome
 * @param {string} bankNameOrSlug - O slug ou nome do banco
 * @returns {string} URL do logo do banco, ou logo padrão se não encontrado
 */
function getBankLogo(bankNameOrSlug) {
    if (!bankNameOrSlug) return bankLogos['default'];
    
    // Normaliza o nome/slug para comparação (lowercase e remove acentos)
    const normalized = bankNameOrSlug.toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .replace(/[^a-z0-9]/g, '-');
    
    // Tenta encontrar o logo pelo slug normalizado
    if (bankLogos[normalized]) return bankLogos[normalized];
    
    // Para nomes compostos, tenta encontrar pela primeira palavra
    const firstWord = normalized.split('-')[0];
    for (const key in bankLogos) {
        if (key.includes(firstWord) || firstWord.includes(key)) return bankLogos[key];
    }
    
    // Se não encontrar, retorna o logo padrão
    return bankLogos['default'];
}

<?php
if (!defined('ABS_PATH')) {
  exit('ABS_PATH is not loaded. Direct access is not allowed.');
}

/**
 * Properly accented Portuguese legal/info pages (one-time DB update via theme).
 * Avoids Osclass SQL importer limitations with UTF-8 accents.
 */
function del_acv_legal_pages_content() {
  return array(
    'about-us' => array(
      'title' => 'Sobre nós',
      'text' => '<p><strong>Anúncios Cabo Verde</strong> é a plataforma de classificados online dedicada a ligar compradores e vendedores em todas as ilhas de Cabo Verde.</p><p>A nossa missão é oferecer um mercado simples, seguro e acessível para anunciar e encontrar produtos, serviços, imóveis, veículos e oportunidades de emprego.</p><p>Estamos empenhados em construir confiança na comunidade cabo-verdiana, com regras claras, ferramentas de denúncia e apoio aos utilizadores.</p>',
    ),
    'como-funciona' => array(
      'title' => 'Como funciona',
      'text' => '<ol><li><strong>Crie uma conta</strong> — o registo é gratuito e rápido.</li><li><strong>Publique o seu anúncio</strong> — escolha a categoria, localização (ilha/cidade), título, descrição, preço em CVE e fotos.</li><li><strong>Receba contactos</strong> — os interessados contactam-no através do formulário ou telefone indicado.</li><li><strong>Negocie com segurança</strong> — encontre-se em locais públicos e nunca envie dinheiro antecipadamente a desconhecidos.</li></ol><p>Também pode guardar anúncios nos <strong>Favoritos</strong> e gerir os seus anúncios na área <strong>A minha conta</strong>.</p>',
    ),
    'terms' => array(
      'title' => 'Termos e condições',
      'text' => '<p>Ao utilizar o Anúncios Cabo Verde, aceita estes termos.</p><h3>1. Utilização da plataforma</h3><p>Pode publicar e consultar anúncios para fins legais. É responsável pela veracidade do conteúdo que publica.</p><h3>2. Conteúdo proibido</h3><p>É proibido publicar conteúdo ilegal, fraudulento, discriminatório, ofensivo, ou que viole direitos de terceiros.</p><h3>3. Contas</h3><p>Deve manter os dados da conta atualizados. Podemos suspender contas que violem as regras.</p><h3>4. Responsabilidade</h3><p>Anúncios Cabo Verde é um intermediário. As transações ocorrem entre utilizadores. Não somos parte do contrato de compra e venda.</p><h3>5. Alterações</h3><p>Podemos atualizar estes termos. A versão publicada nesta página é a válida.</p><p><em>Nota: texto base — reveja com aconselhamento jurídico antes da publicação final.</em></p>',
    ),
    'privacy' => array(
      'title' => 'Política de privacidade',
      'text' => '<p>Respeitamos a sua privacidade.</p><h3>Dados que recolhemos</h3><p>Nome, email, telefone (opcional), dados de anúncios, e informação técnica necessária ao funcionamento do site (IP, cookies essenciais).</p><h3>Finalidade</h3><p>Criar e gerir a conta, publicar anúncios, permitir contacto entre utilizadores, prevenir fraude e melhorar o serviço.</p><h3>Partilha</h3><p>Não vendemos os seus dados pessoais. Podemos partilhar dados quando exigido por lei ou para proteção da plataforma.</p><h3>Os seus direitos</h3><p>Pode solicitar acesso, correção ou eliminação dos seus dados através do formulário de contacto.</p><p><em>Nota: texto base — reveja com aconselhamento jurídico antes da publicação final.</em></p>',
    ),
    'cookies' => array(
      'title' => 'Política de cookies',
      'text' => '<p>Utilizamos cookies essenciais para manter a sessão, preferências de idioma e segurança do site.</p><p>Cookies técnicos são necessários ao funcionamento. Pode gerir cookies no seu navegador. Desativar cookies essenciais pode afetar o login e a publicação de anúncios.</p><p>Se utilizarmos ferramentas de análise no futuro, atualizaremos esta política e pediremos consentimento quando aplicável.</p>',
    ),
    'faq' => array(
      'title' => 'Perguntas frequentes',
      'text' => '<h3>Como publico um anúncio?</h3><p>Crie uma conta, clique em <strong>Publicar anúncio</strong>, escolha a categoria e a localização, preencha os detalhes, adicione fotos e publique.</p><h3>É gratuito?</h3><p>Sim, a publicação básica é gratuita.</p><h3>Que moeda devo usar?</h3><p>Utilize <strong>CVE</strong> (escudo cabo-verdiano).</p><h3>Como contacto um vendedor?</h3><p>Abra o anúncio e use o formulário de contacto ou o telefone indicado.</p><h3>Como recupero a palavra-passe?</h3><p>Na página de entrada escolha recuperar palavra-passe e siga o email recebido.</p><h3>Como denuncio um anúncio?</h3><p>Consulte a página Como denunciar um anúncio no rodapé do site.</p>',
    ),
    'seguranca' => array(
      'title' => 'Segurança e prevenção de fraude',
      'text' => '<h3>Dicas de segurança</h3><ul><li>Encontre-se em locais públicos e movimentados.</li><li>Não envie dinheiro antecipadamente a desconhecidos.</li><li>Desconfie de preços demasiado baixos ou urgência excessiva.</li><li>Verifique o produto antes de pagar.</li><li>Nunca partilhe códigos de confirmação ou palavra-passe.</li></ul><h3>Sinais de fraude</h3><p>Pedidos de pagamento fora da plataforma para contas desconhecidas, vendedores que evitam encontros presenciais, ou pedidos de dados bancários sensíveis.</p><p>Se suspeitar de fraude, denuncie o anúncio e contacte-nos.</p>',
    ),
    'regras-anuncios' => array(
      'title' => 'Regras de publicação de anúncios',
      'text' => '<ul><li>Publique apenas anúncios verdadeiros e legais.</li><li>Use a categoria correta e indique a localização real (ilha/cidade).</li><li>Inclua fotos próprias do artigo, sempre que possível.</li><li>Indique o preço em CVE de forma clara.</li><li>Não publique conteúdo duplicado, spam ou anúncios enganosos.</li><li>Não publique produtos ou serviços proibidos por lei.</li></ul><p>Anúncios que violem estas regras podem ser rejeitados ou removidos.</p>',
    ),
    'como-denunciar' => array(
      'title' => 'Como denunciar um anúncio',
      'text' => '<ol><li>Abra o anúncio em causa.</li><li>Utilize a opção de denúncia / marcar como spam disponível na página do anúncio.</li><li>Em alternativa, envie-nos uma mensagem através da página de Contacto com o link do anúncio e o motivo.</li></ol><p>A nossa equipa analisa as denúncias e pode remover anúncios ou bloquear utilizadores abusivos.</p>',
    ),
    'contacto-completo' => array(
      'title' => 'Informações de contacto',
      'text' => '<p><strong>Anúncios Cabo Verde</strong></p><ul><li>Email: suporte@anuncioscaboverde.com</li><li>Website: https://www.anuncioscaboverde.com/</li><li>Formulário: página de Contacto do site</li></ul><p>Respondemos normalmente em até 48 horas úteis.</p><p><em>Atualize o telefone e morada nesta página quando estiverem disponíveis.</em></p>',
    ),
  );
}

/**
 * One-time: write accented Portuguese titles/bodies into t_pages_description.
 */
function del_acv_legal_accents() {
  if (osc_get_preference('acv_legal_accents_v1', 'theme-delta') == '1') {
    return;
  }
  try {
    $pages = del_acv_legal_pages_content();
    foreach ($pages as $internal => $row) {
      $page = Page::newInstance()->findByInternalName($internal);
      if (empty($page['pk_i_id'])) {
        continue;
      }
      Page::newInstance()->updateDescription(
        (int) $page['pk_i_id'],
        'pt_PT',
        $row['title'],
        $row['text']
      );
    }
    osc_set_preference('acv_legal_accents_v1', '1', 'theme-delta');
    osc_reset_preferences();
  } catch (Exception $e) {
    // Never break frontend if page update fails
  }
}
osc_add_hook('init', 'del_acv_legal_accents', 10);

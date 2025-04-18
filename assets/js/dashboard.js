jQuery(document).ready(function () {
    jQuery.ajax({
        url: ajaxurl,
        method: 'POST',
        dataType: 'json',
        data: {
            action: 'alphaform_get_form_widget_count',
            result: 'form',
            nonce: alphaFormDashboardVars.nonce,
        },
        success: function (res) {
            if (res.success) {
                const data = res.data;

                jQuery('#alpha-form-count').text(data.total_forms);
                jQuery('#alpha-response-count').text(data.total_responses);
                jQuery('#alpha-integrations-count').text(data.total_integrations);
                jQuery('#alpha-last-submit').text(data.last_submit || 'N/A');

                jQuery('.alpha-skeleton').removeClass('alpha-skeleton');
            }
        }
    });

    const promoCard = jQuery('#alpha-promo-card');
    const today = new Date().toISOString().slice(0, 10); // Formato YYYY-MM-DD
    const promoUrl = alphaFormDashboardVars.plugin_url + 'assets/data/promo.json';
    console.log(promoUrl)

    fetch(promoUrl)
        .then(response => response.json())
        .then(data => {
            let selectedDate = null;

            if (data[today]) {
                selectedDate = today;
            } else {
                // Pega a última data anterior disponível
                const availableDates = Object.keys(data).sort().reverse();
                for (const date of availableDates) {
                    if (date < today) {
                        selectedDate = date;
                        break;
                    }
                }
            }

            if (!selectedDate) return;

            const promo = data[selectedDate];
            promoCard.find('.alpha-promo-title').text(promo.title);
            promoCard.find('.alpha-promo-text').text(promo.text);
            promoCard.find('.alpha-promo-cta').text(promo.cta_text);
            promoCard.attr('href', promo.cta_url);

            if (promo.bg_image) {
                promoCard.css('background-image', `url(${promo.bg_image})`);
            }

            promoCard.show(); // exibe o card
        })
        .catch(error => {
            console.warn('[AlphaForm] Falha ao carregar promoções:', error);
        });
});

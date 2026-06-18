(() => {
    const cfg = window.MvAdminAi || {};
    const csrf = () => document.querySelector('meta[name="csrf-token"]')?.content || '';

    const val = (form, name) => form.querySelector(`[name="${name}"]`)?.value?.trim() || '';
    const selectText = (form, name) => {
        const el = form.querySelector(`[name="${name}"]`);
        return el?.options?.[el.selectedIndex]?.text?.trim() || '';
    };
    const checkedValues = (form, name) =>
        [...form.querySelectorAll(`[name="${name}"]:checked`)].map((el) => el.value);

    function findForm(el) {
        return el.closest('form') || el.closest('.js-admin-ai-form');
    }

    function collectContext(form, type) {
        const base = {
            site_name: 'MV Miami Rental',
            location: 'Miami, Florida',
        };

        if (type === 'vehicle') {
            const make = val(form, 'make');
            const model = val(form, 'model');
            return {
                ...base,
                make,
                model,
                year: val(form, 'year'),
                category: selectText(form, 'category_id'),
                display_title: val(form, 'title_en') || `${make} ${model}`.trim(),
                price_per_day: val(form, 'price_per_day'),
                seats: val(form, 'seats'),
                bags: val(form, 'bags'),
                status: val(form, 'status'),
                existing_description: val(form, 'description_en'),
            };
        }

        if (type === 'property') {
            return {
                ...base,
                title: val(form, 'title_en'),
                property_type: selectText(form, 'property_type_id'),
                address: val(form, 'address_line1'),
                neighborhood: val(form, 'neighborhood'),
                city: val(form, 'city'),
                state: val(form, 'state'),
                bedrooms: val(form, 'bedrooms'),
                bathrooms: val(form, 'bathrooms'),
                sqft: val(form, 'sqft'),
                max_guests: val(form, 'max_guests'),
                price_per_month: val(form, 'price_per_month'),
                price_per_night: val(form, 'price_per_night'),
                amenities: checkedValues(form, 'amenities[]'),
                pets_allowed: form.querySelector('[name="pets_allowed"]')?.checked ? 'yes' : 'no',
                furnished: form.querySelector('[name="furnished"]')?.checked ? 'yes' : 'no',
                existing_description: val(form, 'description_en'),
            };
        }

        if (type === 'blog') {
            const enField = form.dataset.aiDescEn || 'content_en';
            return {
                ...base,
                slug: val(form, 'slug'),
                title: val(form, 'title_en'),
                status: val(form, 'status'),
                existing_content: val(form, enField),
            };
        }

        return base;
    }

    function setBusy(btn, busy, labelBusy) {
        if (!btn) return;
        if (busy) {
            btn.dataset.prevText = btn.textContent;
            btn.disabled = true;
            btn.classList.add('opacity-60', 'cursor-wait');
            btn.textContent = labelBusy;
        } else {
            btn.disabled = false;
            btn.classList.remove('opacity-60', 'cursor-wait');
            btn.textContent = btn.dataset.prevText || btn.textContent;
        }
    }

    async function postJson(url, payload) {
        const res = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                'X-CSRF-TOKEN': csrf(),
            },
            body: JSON.stringify(payload),
        });

        const data = await res.json().catch(() => ({}));
        if (!res.ok || !data.ok) {
            throw new Error(data.message || 'Request failed.');
        }

        return data.data;
    }

    document.addEventListener('click', async (e) => {
        const descBtn = e.target.closest('.js-ai-generate-desc');
        if (descBtn) {
            e.preventDefault();
            const form = findForm(descBtn);
            if (!form) return;

            const type = descBtn.dataset.aiType || form.dataset.aiType;
            if (!type) return;

            const enField = descBtn.dataset.aiEnField || form.dataset.aiDescEn || 'description_en';
            const esField = descBtn.dataset.aiEsField || form.dataset.aiDescEs || 'description_es';

            setBusy(descBtn, true, 'Generating…');

            try {
                const data = await postJson(cfg.descriptionsUrl, {
                    type,
                    context: collectContext(form, type),
                });

                const enEl = form.querySelector(`[name="${enField}"]`);
                const esEl = form.querySelector(`[name="${esField}"]`);
                if (enEl && data.description_en) enEl.value = data.description_en;
                if (esEl && data.description_es) esEl.value = data.description_es;
            } catch (err) {
                alert(err.message || 'Could not generate descriptions.');
            } finally {
                setBusy(descBtn, false);
            }

            return;
        }

        const seoBtn = e.target.closest('.js-ai-generate-seo');
        if (seoBtn) {
            e.preventDefault();
            const section = seoBtn.closest('.js-admin-seo-section');
            const form = findForm(seoBtn);
            if (!section || !form) return;

            const type = section.dataset.aiType || form.dataset.aiType;
            const prefix = section.dataset.seoPrefix || 'seo';
            if (!type) return;

            setBusy(seoBtn, true, 'Generating…');

            try {
                const data = await postJson(cfg.seoUrl, {
                    type,
                    context: collectContext(form, type),
                });

                const map = {
                    meta_title: `[name="${prefix}[meta_title]"]`,
                    meta_description: `[name="${prefix}[meta_description]"]`,
                    meta_keywords: `[name="${prefix}[meta_keywords]"]`,
                    og_title: `[name="${prefix}[og_title]"]`,
                    og_description: `[name="${prefix}[og_description]"]`,
                };

                Object.entries(map).forEach(([key, selector]) => {
                    if (!data[key]) return;
                    const el = form.querySelector(selector);
                    if (el) el.value = data[key];
                });
            } catch (err) {
                alert(err.message || 'Could not generate SEO fields.');
            } finally {
                setBusy(seoBtn, false);
            }
        }
    });
})();

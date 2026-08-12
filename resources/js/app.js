const availabilityForm = document.querySelector('#availability-form');
const availabilityModal = document.querySelector('#availability-modal');
const availabilityResults = document.querySelector('#availability-results');
const availabilityMessage = document.querySelector('#availability-message');
const availabilityModalAction = document.querySelector('#availability-modal-action');
const siteHeader = document.querySelector('[data-site-header]');
let lastAvailabilityData = null;
let currentPreviewRoom = null;
let currentCarouselIndex = 0;

function updateHeaderSize() {
    siteHeader?.classList.toggle('is-scrolled', window.scrollY > 16);
}

updateHeaderSize();
window.addEventListener('scroll', updateHeaderSize, { passive: true });

const revealItems = [...document.querySelectorAll('[data-scroll-reveal]')];
const navLinks = [...document.querySelectorAll('[data-nav-link]')];
const navSections = [...document.querySelectorAll('[data-nav-section]')];
const contactMessage = document.querySelector('[data-contact-message]');
const contactCount = document.querySelector('[data-contact-count]');
const roomManagement = document.querySelector('[data-room-management]');
const sweetAlertModal = document.querySelector('[data-swal-modal]');
const sweetAlertIcon = document.querySelector('[data-swal-icon]');
const sweetAlertTitle = document.querySelector('[data-swal-title]');
const sweetAlertMessage = document.querySelector('[data-swal-message]');
const sweetAlertConfirm = document.querySelector('[data-swal-confirm]');
const sweetAlertCancel = document.querySelector('[data-swal-cancel]');
let sweetAlertResolver = null;

function showSweetAlert({
    type = 'success',
    title = '',
    message = '',
    confirmText = 'OK',
    cancelText = '',
} = {}) {
    if (!sweetAlertModal || !sweetAlertIcon || !sweetAlertTitle || !sweetAlertMessage || !sweetAlertConfirm || !sweetAlertCancel) {
        return Promise.resolve(window.confirm(message || title));
    }

    sweetAlertModal.classList.remove('hidden', 'is-success', 'is-error', 'is-warning');
    sweetAlertModal.classList.add(`is-${type}`);
    sweetAlertModal.setAttribute('aria-hidden', 'false');
    sweetAlertIcon.textContent = type === 'error' ? 'Fix' : type === 'warning' ? '?' : type === 'info' ? 'Info' : 'Done';
    sweetAlertTitle.textContent = title || {
        error: 'A few details need attention',
        info: 'Please note',
        warning: 'Confirm action',
        success: 'All set',
    }[type] || 'All set';
    sweetAlertMessage.textContent = message;
    sweetAlertConfirm.textContent = confirmText;
    sweetAlertCancel.textContent = cancelText || 'Cancel';
    sweetAlertCancel.classList.toggle('hidden', !cancelText);
    sweetAlertConfirm.focus();

    return new Promise((resolve) => {
        sweetAlertResolver = resolve;
    });
}

function closeSweetAlert(result = false) {
    if (!sweetAlertModal) {
        return;
    }

    sweetAlertModal.classList.add('hidden');
    sweetAlertModal.setAttribute('aria-hidden', 'true');
    sweetAlertResolver?.(result);
    sweetAlertResolver = null;
}

sweetAlertConfirm?.addEventListener('click', () => closeSweetAlert(true));
sweetAlertCancel?.addEventListener('click', () => closeSweetAlert(false));
sweetAlertModal?.addEventListener('click', (event) => {
    if (event.target === sweetAlertModal) {
        closeSweetAlert(false);
    }
});

const feedback = document.querySelector('[data-swal-feedback]');

if (feedback) {
    showSweetAlert({
        type: feedback.dataset.type || 'success',
        title: feedback.dataset.title || '',
        message: feedback.dataset.message || '',
    });
}

document.querySelectorAll('[data-confirm-action]').forEach((form) => {
    form.addEventListener('submit', async (event) => {
        if (form.dataset.confirmed === 'true') {
            return;
        }

        event.preventDefault();

        const confirmed = await showSweetAlert({
            type: 'warning',
            title: form.dataset.confirmTitle || 'Confirm action',
            message: form.dataset.confirmAction || 'Continue with this action?',
            confirmText: form.dataset.confirmText || 'Yes, continue',
            cancelText: form.dataset.cancelText || 'Keep editing',
        });

        if (!confirmed) {
            return;
        }

        form.dataset.confirmed = 'true';
        form.requestSubmit(event.submitter);
    });
});

document.querySelectorAll('form').forEach((form) => {
    if (form.matches('#availability-form')) {
        return;
    }

    form.addEventListener('submit', (event) => {
        if (event.defaultPrevented) {
            return;
        }

        const submitter = form.querySelector('button[type="submit"]');

        if (!submitter || submitter.dataset.loadingBound === 'true') {
            return;
        }

        submitter.dataset.loadingBound = 'true';
        submitter.dataset.originalText = submitter.textContent.trim();
        submitter.textContent = submitter.dataset.loadingText || 'Working...';
        submitter.disabled = true;
    });
});

if ('IntersectionObserver' in window) {
    const revealObserver = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                revealObserver.unobserve(entry.target);
            }
        });
    }, { rootMargin: '0px 0px -12% 0px', threshold: 0.1 });

    revealItems.forEach((item) => revealObserver.observe(item));
} else {
    revealItems.forEach((item) => item.classList.add('is-visible'));
}

if (navLinks.length && navSections.length && 'IntersectionObserver' in window) {
    const setActiveNav = (sectionId) => {
        navLinks.forEach((link) => {
            link.classList.toggle('nav-link-active', link.dataset.navLink === sectionId);
        });
    };

    const navObserver = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                setActiveNav(entry.target.dataset.navSection);
            }
        });
    }, { rootMargin: '-28% 0px -55% 0px', threshold: 0.01 });

    navSections.forEach((section) => navObserver.observe(section));
}

if (contactMessage && contactCount) {
    const updateContactCount = () => {
        contactCount.textContent = contactMessage.value.length;
    };

    contactMessage.addEventListener('input', updateContactCount);
    updateContactCount();
}

if (roomManagement) {
    const searchInput = roomManagement.querySelector('[data-room-search]');
    const statusFilter = roomManagement.querySelector('[data-room-status-filter]');
    const sortSelect = roomManagement.querySelector('[data-room-sort]');
    const rows = [...roomManagement.querySelectorAll('[data-room-row]')];
    const tableBody = rows[0]?.parentElement;
    const visibleCount = roomManagement.querySelector('[data-room-visible-count]');
    const emptyState = roomManagement.querySelector('[data-room-empty-state]');

    const sortRows = () => {
        const sortValue = sortSelect?.value || 'name';
        const sortedRows = [...rows].sort((a, b) => {
            if (sortValue === 'rate_desc') {
                return Number(b.dataset.rate) - Number(a.dataset.rate);
            }

            if (sortValue === 'rate_asc') {
                return Number(a.dataset.rate) - Number(b.dataset.rate);
            }

            if (sortValue === 'guests_desc') {
                return Number(b.dataset.guests) - Number(a.dataset.guests);
            }

            return a.dataset.name.localeCompare(b.dataset.name);
        });

        sortedRows.forEach((row) => tableBody?.appendChild(row));
    };

    const applyRoomFilters = () => {
        const query = (searchInput?.value || '').trim().toLowerCase();
        const status = statusFilter?.value || 'all';
        let count = 0;

        rows.forEach((row) => {
            const matchesQuery = !query || row.dataset.search.includes(query);
            const matchesStatus = status === 'all' || row.dataset.status === status;
            const isVisible = matchesQuery && matchesStatus;

            row.classList.toggle('hidden', !isVisible);
            if (isVisible) {
                count += 1;
            }
        });

        if (visibleCount) {
            visibleCount.textContent = count;
        }

        emptyState?.classList.toggle('hidden', count > 0);
    };

    searchInput?.addEventListener('input', applyRoomFilters);
    statusFilter?.addEventListener('change', applyRoomFilters);
    sortSelect?.addEventListener('change', () => {
        sortRows();
        applyRoomFilters();
    });

    sortRows();
    applyRoomFilters();
}

function initCarousel(carousel) {
    const slides = [...carousel.querySelectorAll('[data-carousel-slide]')];
    const dots = [...carousel.querySelectorAll('[data-carousel-dot]')];
    const previous = carousel.querySelector('[data-carousel-prev]');
    const next = carousel.querySelector('[data-carousel-next]');
    let index = Math.max(0, slides.findIndex((slide) => slide.classList.contains('is-active')));

    if (slides.length < 2) {
        previous?.classList.add('hidden');
        next?.classList.add('hidden');
        return;
    }

    const show = (nextIndex) => {
        index = (nextIndex + slides.length) % slides.length;

        slides.forEach((slide, slideIndex) => {
            slide.classList.toggle('is-active', slideIndex === index);
        });

        dots.forEach((dot, dotIndex) => {
            dot.classList.toggle('is-active', dotIndex === index);
            dot.setAttribute('aria-current', dotIndex === index ? 'true' : 'false');
        });
    };

    previous?.addEventListener('click', () => show(index - 1));
    next?.addEventListener('click', () => show(index + 1));

    dots.forEach((dot) => {
        dot.addEventListener('click', () => show(Number(dot.dataset.carouselDot)));
    });

    if (carousel.dataset.carouselAutoplay === 'true') {
        window.setInterval(() => show(index + 1), 6500);
    }
}

document.querySelectorAll('[data-carousel]').forEach(initCarousel);

const roomSelect = document.querySelector('[data-room-select]');
const roomEmptyState = document.querySelector('[data-room-empty]');
const roomPreviewPanels = [...document.querySelectorAll('[data-room-preview-panel]')];
const summaryRoom = document.querySelector('[data-summary-room]');
const summaryRate = document.querySelector('[data-summary-rate]');
const reservationForm = document.querySelector('[data-reservation-form]');
const reservationArrivalDate = document.querySelector('[data-arrival-date]');
const reservationArrivalSelect = document.querySelector('[data-reservation-arrival-select]');
const startsAtInput = document.querySelector('[data-starts-at-input]');
const reservationSubmit = document.querySelector('[data-reservation-submit]');
const serviceForm = document.querySelector('[data-service-form]');

if (serviceForm) {
    const fields = {
        name: serviceForm.querySelector('[data-service-name]'),
        description: serviceForm.querySelector('[data-service-description]'),
        image: serviceForm.querySelector('[data-service-image]'),
        window: serviceForm.querySelector('[data-service-window]'),
        rate: serviceForm.querySelector('[data-service-rate]'),
        guests: serviceForm.querySelector('[data-service-guests]'),
        size: serviceForm.querySelector('[data-service-size]'),
        amenities: serviceForm.querySelector('[data-service-amenities]'),
    };

    const preview = {
        image: serviceForm.querySelector('[data-service-preview-image]'),
        name: serviceForm.querySelector('[data-service-preview-name]'),
        description: serviceForm.querySelector('[data-service-preview-description]'),
        window: serviceForm.querySelector('[data-service-preview-window]'),
        rate: serviceForm.querySelector('[data-service-preview-rate]'),
        guests: serviceForm.querySelector('[data-service-preview-guests]'),
        size: serviceForm.querySelector('[data-service-preview-size]'),
        amenities: serviceForm.querySelector('[data-service-preview-amenities]'),
    };

    const updateServicePreview = () => {
        if (preview.image) {
            preview.image.src = fields.image?.files?.[0]
                ? URL.createObjectURL(fields.image.files[0])
                : preview.image.dataset.defaultImage;
        }

        if (preview.name) {
            preview.name.textContent = fields.name?.value.trim() || 'Room name';
        }

        if (preview.description) {
            preview.description.textContent = fields.description?.value.trim() || 'A short guest-facing description will appear here.';
        }

        if (preview.window) {
            preview.window.textContent = fields.window?.value || '60';
        }

        if (preview.rate) {
            const rate = Number(fields.rate?.value || 0);
            preview.rate.textContent = rate === 0 ? 'Free' : `$${rate.toFixed(2)}`;
        }

        if (preview.guests) {
            preview.guests.textContent = fields.guests?.value || '2';
        }

        if (preview.size) {
            preview.size.textContent = fields.size?.value.trim() || 'Size not set';
        }

        if (preview.amenities) {
            const amenities = (fields.amenities?.value || '')
                .split(/\r\n|\r|\n/)
                .map((item) => item.trim())
                .filter(Boolean);

            preview.amenities.innerHTML = amenities.length
                ? amenities.map((amenity) => `<span class="rounded-full border border-stone-200 px-3 py-1 text-xs font-medium text-stone-600">${escapeHtml(amenity)}</span>`).join('')
                : '<span class="text-sm text-stone-500">Amenities will appear here.</span>';
        }
    };

    Object.values(fields).forEach((field) => field?.addEventListener('input', updateServicePreview));
    fields.image?.addEventListener('change', updateServicePreview);
    updateServicePreview();
}

document.querySelectorAll('[data-upload-dropzone]').forEach((dropzone) => {
    const input = dropzone.querySelector('[data-upload-input]');
    const label = dropzone.querySelector('[data-upload-label]');
    const defaultLabel = label?.textContent;

    const updateUploadLabel = () => {
        if (!label || !input?.files?.length) {
            if (label && defaultLabel) {
                label.textContent = defaultLabel;
            }

            return;
        }

        label.textContent = input.files.length === 1
            ? input.files[0].name
            : `${input.files.length} images selected`;
    };

    ['dragenter', 'dragover'].forEach((eventName) => {
        dropzone.addEventListener(eventName, (event) => {
            event.preventDefault();
            dropzone.classList.add('is-dragover');
        });
    });

    ['dragleave', 'drop'].forEach((eventName) => {
        dropzone.addEventListener(eventName, () => {
            dropzone.classList.remove('is-dragover');
        });
    });

    dropzone.addEventListener('drop', (event) => {
        event.preventDefault();

        if (!input || !event.dataTransfer?.files?.length) {
            return;
        }

        const files = [...event.dataTransfer.files].filter((file) => file.type.startsWith('image/'));
        const transfer = new DataTransfer();

        files.forEach((file) => transfer.items.add(file));
        input.files = transfer.files;
        input.dispatchEvent(new Event('change', { bubbles: true }));
        updateUploadLabel();
    });

    input?.addEventListener('change', updateUploadLabel);
    updateUploadLabel();
});

document.querySelectorAll('[data-password-toggle]').forEach((toggle) => {
    const wrapper = toggle.closest('.relative');
    const input = wrapper?.querySelector('[data-password-input]');
    const showIcon = toggle.querySelector('.password-icon-show');
    const hideIcon = toggle.querySelector('.password-icon-hide');

    toggle.addEventListener('click', () => {
        if (!input) {
            return;
        }

        const isVisible = input.type === 'text';
        input.type = isVisible ? 'password' : 'text';
        toggle.setAttribute('aria-pressed', isVisible ? 'false' : 'true');
        toggle.setAttribute('aria-label', isVisible ? 'Show password' : 'Hide password');
        showIcon?.classList.toggle('hidden', !isVisible);
        hideIcon?.classList.toggle('hidden', isVisible);
        input.focus();
    });
});

function updateSelectedRoomPreview() {
    if (!roomSelect) {
        return;
    }

    const selectedOption = roomSelect.selectedOptions[0];
    const selectedRoomId = roomSelect.value;
    const hasSelection = selectedRoomId !== '';

    roomEmptyState?.classList.toggle('hidden', hasSelection);

    roomPreviewPanels.forEach((panel) => {
        panel.classList.toggle('hidden', panel.dataset.roomPreviewPanel !== selectedRoomId);
    });

    if (summaryRoom) {
        summaryRoom.textContent = hasSelection ? selectedOption.dataset.roomName : 'Select a room';
    }

    if (summaryRate) {
        summaryRate.textContent = hasSelection ? selectedOption.dataset.roomRate : 'Shown by room';
    }

    refreshReservationSlots();
}

roomSelect?.addEventListener('change', updateSelectedRoomPreview);
reservationArrivalDate?.addEventListener('change', refreshReservationSlots);
reservationArrivalSelect?.addEventListener('change', () => {
    if (startsAtInput) {
        startsAtInput.value = reservationArrivalSelect.value;
    }

    updateReservationSubmitState();
});
updateSelectedRoomPreview();

async function refreshReservationSlots() {
    if (!reservationForm || !roomSelect || !reservationArrivalDate || !reservationArrivalSelect || !startsAtInput) {
        return;
    }

    const roomId = Number(roomSelect.value);
    const arrivalDate = reservationArrivalDate.value;
    const preferredValue = reservationArrivalSelect.dataset.selectedArrival || startsAtInput.value;

    reservationArrivalSelect.innerHTML = '<option value="">Loading available times...</option>';
    reservationArrivalSelect.disabled = true;
    startsAtInput.value = '';
    updateReservationSubmitState();

    if (!roomId || !arrivalDate) {
        reservationArrivalSelect.innerHTML = '<option value="">Select a room and date first</option>';
        updateReservationSubmitState();
        return;
    }

    try {
        const url = `${reservationForm.dataset.availabilityUrl}?${new URLSearchParams({ date: arrivalDate }).toString()}`;
        const response = await fetch(url, { headers: { Accept: 'application/json' } });
        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.message || 'Availability could not be checked.');
        }

        const room = data.rooms.find((item) => item.id === roomId);
        const slots = room?.slots ?? [];

        if (!slots.length) {
            reservationArrivalSelect.innerHTML = '<option value="">No available times for this room/date</option>';
            updateReservationSubmitState();
            return;
        }

        reservationArrivalSelect.innerHTML = [
            '<option value="">Select an available time</option>',
            ...slots.map((slot) => `<option value="${escapeHtml(slot.value)}">${escapeHtml(slot.label)}</option>`),
        ].join('');
        reservationArrivalSelect.disabled = false;

        const selectedSlot = slots.find((slot) => slot.value === preferredValue) ?? slots[0];
        reservationArrivalSelect.value = selectedSlot.value;
        startsAtInput.value = selectedSlot.value;
        reservationArrivalSelect.dataset.selectedArrival = '';
        updateReservationSubmitState();
    } catch (error) {
        reservationArrivalSelect.innerHTML = `<option value="">${escapeHtml(error.message)}</option>`;
        updateReservationSubmitState();
    }
}

function updateReservationSubmitState() {
    if (reservationSubmit && startsAtInput) {
        reservationSubmit.disabled = startsAtInput.value === '';
    }
}

function escapeHtml(value) {
    return String(value ?? '').replace(/[&<>"']/g, (character) => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;',
    }[character]));
}

function closeAvailabilityModal() {
    availabilityModal?.classList.add('hidden');
    currentPreviewRoom = null;
    setModalAction('close');
}

function openAvailabilityModal() {
    availabilityModal?.classList.remove('hidden');
}

function setModalAction(mode) {
    if (!availabilityModalAction) {
        return;
    }

    availabilityModalAction.textContent = mode === 'back' ? 'Back' : 'Close';
    availabilityModalAction.dataset.modalMode = mode;
}

function renderAvailability(data) {
    lastAvailabilityData = data;
    currentPreviewRoom = null;
    setModalAction('close');
    availabilityMessage.textContent = data.message;

    const rows = data.rooms.map((room) => {
        const amenities = room.amenities.slice(0, 4).map((amenity) => `
            <span class="rounded-full border border-stone-200 px-2.5 py-1 text-xs font-medium text-stone-600">${escapeHtml(amenity)}</span>
        `).join('');

        return `
            <tr>
                <td data-label="Room">
                    <div class="flex items-center gap-3">
                        <img src="${escapeHtml(room.image_url)}" alt="" class="size-14 rounded-md object-cover">
                        <span>
                            <strong class="block font-semibold text-stone-950">${escapeHtml(room.name)}</strong>
                            <span class="text-stone-500">${room.duration_minutes} min window</span>
                        </span>
                    </div>
                </td>
                <td data-label="Rate">${escapeHtml(room.price)}</td>
                <td data-label="Amenities">
                    <div class="flex flex-wrap gap-2">${amenities}</div>
                </td>
                <td data-label="Action" class="md:text-right">
                    <button class="btn btn-primary min-h-9 px-3 py-1.5" type="button" data-room-preview="${room.id}">View Room</button>
                </td>
            </tr>
        `;
    }).join('');

    availabilityResults.innerHTML = `
        <table class="data-table responsive-table">
            <thead>
                <tr>
                    <th>Room</th>
                    <th>Rate</th>
                    <th>Amenities</th>
                    <th class="text-right">Action</th>
                </tr>
            </thead>
            <tbody>${rows}</tbody>
        </table>
    `;
}

function renderRoomPreview(room) {
    currentPreviewRoom = room;
    setModalAction('back');
    availabilityMessage.textContent = `${room.name} details and available arrival times.`;

    const selectedImage = room.gallery[currentCarouselIndex] ?? room.image_url;
    const slotOptions = room.slots.map((slot) => `
        <option value="${escapeHtml(slot.value)}">${escapeHtml(slot.label)}</option>
    `).join('');

    availabilityResults.innerHTML = `
        <div class="grid gap-0 lg:grid-cols-[1.05fr_0.95fr]">
            <div class="bg-stone-100 p-4">
                <div class="carousel-frame">
                    <img src="${escapeHtml(selectedImage)}" alt="${escapeHtml(room.name)}" class="h-80 w-full rounded-lg object-cover">
                    <button class="carousel-control left-3" type="button" data-carousel-prev aria-label="Previous room image">&lsaquo;</button>
                    <button class="carousel-control right-3" type="button" data-carousel-next aria-label="Next room image">&rsaquo;</button>
                </div>
                <div class="mt-3 flex gap-3 overflow-x-auto pb-1">
                    ${room.gallery.map((imageUrl, index) => `
                        <button class="carousel-thumb ${index === currentCarouselIndex ? 'carousel-thumb-active' : ''}" type="button" data-carousel-index="${index}" aria-label="View room image ${index + 1}">
                            <img src="${escapeHtml(imageUrl)}" alt="" class="h-20 w-28 rounded-md object-cover">
                        </button>
                    `).join('')}
                </div>
            </div>
            <div class="p-5">
                <h3 class="mt-4 text-2xl font-semibold text-stone-950">${escapeHtml(room.name)}</h3>
                <p class="mt-3 text-sm leading-6 text-stone-600">${escapeHtml(room.description)}</p>
                <dl class="mt-5 grid grid-cols-2 gap-3 text-sm">
                    <div class="rounded-md border border-stone-200 p-3">
                        <dt class="text-stone-500">Rate</dt>
                        <dd class="mt-1 font-semibold text-stone-950">${escapeHtml(room.price)}</dd>
                    </div>
                    <div class="rounded-md border border-stone-200 p-3">
                        <dt class="text-stone-500">Window</dt>
                        <dd class="mt-1 font-semibold text-stone-950">${room.duration_minutes} min</dd>
                    </div>
                    <div class="rounded-md border border-stone-200 p-3">
                        <dt class="text-stone-500">Guests</dt>
                        <dd class="mt-1 font-semibold text-stone-950">Up to ${room.max_guests}</dd>
                    </div>
                    <div class="rounded-md border border-stone-200 p-3">
                        <dt class="text-stone-500">Size</dt>
                        <dd class="mt-1 font-semibold text-stone-950">${escapeHtml(room.room_size || 'On request')}</dd>
                    </div>
                </dl>
                <div class="mt-5">
                    <p class="text-sm font-semibold text-stone-950">Amenities</p>
                    <div class="mt-3 flex flex-wrap gap-2">
                        ${room.amenities.map((amenity) => `
                            <span class="rounded-full border border-stone-200 px-3 py-1 text-xs font-medium text-stone-600">${escapeHtml(amenity)}</span>
                        `).join('')}
                    </div>
                </div>
                <div class="mt-6">
                    <label class="field-label">
                        Arrival time
                        <select class="field-control" data-arrival-select ${room.slots.length ? '' : 'disabled'}>
                            ${room.slots.length ? slotOptions : '<option>No slots available</option>'}
                        </select>
                    </label>
                    <a class="btn btn-primary mt-3 w-full ${room.slots.length ? '' : 'pointer-events-none opacity-60'}" href="${room.slots.length ? `/reserve?service_id=${room.id}&starts_at=${encodeURIComponent(room.slots[0].value)}` : '#'}" data-reserve-selected>
                        Reserve Selected Time
                    </a>
                </div>
            </div>
        </div>
    `;
}

availabilityForm?.addEventListener('submit', async (event) => {
    event.preventDefault();

    const button = availabilityForm.querySelector('button[type="submit"]');
    const formData = new FormData(availabilityForm);
    const url = `${availabilityForm.action}?${new URLSearchParams(formData).toString()}`;

    button.disabled = true;
    availabilityMessage.textContent = 'Checking room availability...';
    availabilityResults.innerHTML = '<div class="empty-state text-stone-600">Loading available rooms.</div>';
    openAvailabilityModal();

    try {
        const response = await fetch(url, { headers: { Accept: 'application/json' } });
        const data = await response.json();

        if (! response.ok) {
            throw new Error(data.message || 'Availability could not be checked.');
        }

        renderAvailability(data);
    } catch (error) {
        availabilityMessage.textContent = error.message;
        availabilityResults.innerHTML = '<div class="empty-state text-stone-600">Please choose another date and try again.</div>';
    } finally {
        button.disabled = false;
    }
});

availabilityResults?.addEventListener('click', (event) => {
    const previewButton = event.target.closest('[data-room-preview]');
    const backButton = event.target.closest('[data-availability-back]');

    if (previewButton && lastAvailabilityData) {
        const room = lastAvailabilityData.rooms.find((item) => item.id === Number(previewButton.dataset.roomPreview));

        if (room) {
            currentCarouselIndex = 0;
            renderRoomPreview(room);
        }
    }

    if (backButton && lastAvailabilityData) {
        renderAvailability(lastAvailabilityData);
    }
});

availabilityResults?.addEventListener('change', (event) => {
    const select = event.target.closest('[data-arrival-select]');

    if (select && currentPreviewRoom) {
        const reserveLink = availabilityResults.querySelector('[data-reserve-selected]');
        reserveLink.href = `/reserve?service_id=${currentPreviewRoom.id}&starts_at=${encodeURIComponent(select.value)}`;
    }
});

availabilityResults?.addEventListener('click', (event) => {
    if (!currentPreviewRoom) {
        return;
    }

    const imageCount = currentPreviewRoom.gallery.length;
    const thumb = event.target.closest('[data-carousel-index]');

    if (event.target.closest('[data-carousel-prev]')) {
        currentCarouselIndex = (currentCarouselIndex - 1 + imageCount) % imageCount;
        renderRoomPreview(currentPreviewRoom);
    }

    if (event.target.closest('[data-carousel-next]')) {
        currentCarouselIndex = (currentCarouselIndex + 1) % imageCount;
        renderRoomPreview(currentPreviewRoom);
    }

    if (thumb) {
        currentCarouselIndex = Number(thumb.dataset.carouselIndex);
        renderRoomPreview(currentPreviewRoom);
    }
});

availabilityModalAction?.addEventListener('click', () => {
    if (availabilityModalAction.dataset.modalMode === 'back' && lastAvailabilityData) {
        renderAvailability(lastAvailabilityData);
        return;
    }

    closeAvailabilityModal();
});

availabilityModal?.addEventListener('click', (event) => {
    if (event.target === availabilityModal) {
        closeAvailabilityModal();
    }
});

document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && sweetAlertModal && !sweetAlertModal.classList.contains('hidden')) {
        closeSweetAlert(false);
        return;
    }

    if (event.key === 'Escape') {
        closeAvailabilityModal();
    }
});

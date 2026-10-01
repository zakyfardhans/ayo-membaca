import './bootstrap';
import {
	createIcons,
	ArchiveRestore,
	ArrowLeft,
	ArrowRight,
	ArrowUpRight,
	BookOpen,
	BookOpenCheck,
	BookPlus,
	BookX,
	CalendarDays,
	Check,
	ChevronDown,
	ChevronLeft,
	ChevronRight,
	CircleAlert,
	CircleCheck,
	Eye,
	ImagePlus,
	Layers3,
	Library,
	LibraryBig,
	ListFilter,
	LayoutDashboard,
	Menu,
	PackageX,
	Pencil,
	Plus,
	RotateCcw,
	Search,
	Send,
	SlidersHorizontal,
	Sparkles,
	Sun,
	Trash2,
	TriangleAlert,
	X,
} from 'lucide';

const menuToggle = document.querySelector('[data-menu-toggle]');
const menuClose = document.querySelector('[data-menu-close]');
const globalSearch = document.querySelector('.top-search input');

const setMenuOpen = (open) => {
	document.body.classList.toggle('menu-open', open);
	menuToggle?.setAttribute('aria-expanded', String(open));
};

menuToggle?.addEventListener('click', () => {
	setMenuOpen(!document.body.classList.contains('menu-open'));
});

menuClose?.addEventListener('click', () => setMenuOpen(false));

document.addEventListener('keydown', (event) => {
	if ((event.metaKey || event.ctrlKey) && event.key.toLowerCase() === 'k') {
		event.preventDefault();
		globalSearch?.focus();
	}
});

document.querySelectorAll('[data-dismiss]').forEach((button) => {
	button.addEventListener('click', () => button.closest('.toast')?.remove());
});

document.querySelectorAll('[data-confirm]').forEach((form) => {
	form.addEventListener('submit', (event) => {
		if (!window.confirm(form.dataset.confirm)) event.preventDefault();
	});
});

document.querySelectorAll('[data-cover-input]').forEach((input) => {
	input.addEventListener('change', () => {
		const preview = document.querySelector(input.dataset.coverInput);
		const file = input.files?.[0];
		if (!preview || !file) return;

		const image = document.createElement('img');
		image.src = URL.createObjectURL(file);
		image.alt = 'Pratinjau sampul buku';
		const cover = preview.querySelector('.cover-thumb') ?? preview;
		cover.replaceChildren(image);
	});
});

const postJson = async (url, payload, token) => {
	const response = await fetch(url, {
		method: 'POST',
		headers: {
			Accept: 'application/json',
			'Content-Type': 'application/json',
			'X-CSRF-TOKEN': token,
		},
		body: JSON.stringify(payload),
	});
	const data = await response.json().catch(() => ({}));
	if (!response.ok) throw new Error(data.message || 'Permintaan AI gagal. Coba lagi.');
	return data;
};

document.querySelectorAll('[data-ai-chat]').forEach((widget) => {
	const form = widget.querySelector('[data-ai-chat-form]');
	const questionInput = widget.querySelector('[data-ai-question]');
	const modeInput = widget.querySelector('[data-ai-mode-value]');
	const messageList = widget.querySelector('[data-ai-messages]');
	const token = widget.querySelector('input[name="_token"]')?.value;
	const conversationKey = `arnaru-ai-book-${widget.dataset.bookId}`;
	let conversationId = localStorage.getItem(conversationKey);
	const modePrompts = {
		summary: 'Ringkas buku ini.',
		key_points: 'Jelaskan poin-poin penting buku ini.',
		glossary: 'Buat glosarium istilah penting dari buku ini.',
		reading_guide: 'Buat panduan baca untuk buku ini.',
		quiz: 'Buat kuis singkat tentang isi buku ini.',
	};
	const modeLabels = {
		summary: 'Buat ringkasan',
		key_points: 'Poin-poin penting',
		glossary: 'Glosarium',
		reading_guide: 'Panduan baca',
		quiz: 'Kuis singkat',
	};

	const addMessage = (text, role) => {
		const message = document.createElement('div');
		message.className = `ai-message ai-message-${role}`;
		const paragraph = document.createElement('p');
		paragraph.textContent = text;
		message.append(paragraph);
		messageList.append(message);
		messageList.scrollTop = messageList.scrollHeight;
	};

	widget.querySelectorAll('[data-ai-mode]').forEach((button) => {
		button.addEventListener('click', () => {
			modeInput.value = button.dataset.aiMode;
			if (button.dataset.aiMode === 'ask') {
				questionInput.focus();
				return;
			}
			form.requestSubmit();
		});
	});

	form.addEventListener('submit', async (event) => {
		event.preventDefault();
		const mode = modeInput.value;
		const question = mode === 'ask' ? questionInput.value.trim() : modePrompts[mode];
		if (!question) {
			questionInput.focus();
			return;
		}

		const submit = form.querySelector('[type="submit"]');
		const askButtons = widget.querySelectorAll('[data-ai-mode]');
		const model = widget.querySelector('[data-ai-model]')?.value;
		addMessage(mode === 'ask' ? question : modeLabels[mode], 'user');
		submit.disabled = true;
		askButtons.forEach((button) => { button.disabled = true; });
		const pending = document.createElement('div');
		pending.className = 'ai-message ai-message-assistant ai-message-pending';
		pending.textContent = 'Sedang membaca PDF…';
		messageList.append(pending);
		messageList.scrollTop = messageList.scrollHeight;

		try {
			const data = await postJson(widget.dataset.url, {
				mode,
				question: mode === 'ask' ? question : '',
				conversationId,
				model,
			}, token);
			pending.remove();
			addMessage(data.answer, 'assistant');
			if (data.conversationId) {
				conversationId = data.conversationId;
				localStorage.setItem(conversationKey, conversationId);
			}
			questionInput.value = '';
			modeInput.value = 'ask';
		} catch (error) {
			pending.remove();
			addMessage(error.message, 'error');
		} finally {
			submit.disabled = false;
			askButtons.forEach((button) => { button.disabled = false; });
		}
	});
});

document.querySelectorAll('[data-ai-catalog]').forEach((widget) => {
	const form = widget.querySelector('[data-ai-catalog-form]');
	const promptInput = form.querySelector('[name="prompt"]');
	const results = widget.querySelector('[data-ai-catalog-results]');
	const token = widget.querySelector('input[name="_token"]')?.value;

	form.addEventListener('submit', async (event) => {
		event.preventDefault();
		const button = form.querySelector('[type="submit"]');
		button.disabled = true;
		results.replaceChildren();
		const pending = document.createElement('p');
		pending.className = 'ai-result-status';
		pending.textContent = 'Mencocokkan permintaan dengan katalog…';
		results.append(pending);

		try {
			const data = await postJson(widget.dataset.url, {
				mode: widget.dataset.mode,
				prompt: promptInput.value.trim(),
				model: widget.querySelector('[data-ai-model]')?.value,
			}, token);
			results.replaceChildren();
			if (!data.books?.length) {
				const empty = document.createElement('p');
				empty.className = 'ai-result-status';
				empty.textContent = data.message || 'Tidak ada buku yang cocok.';
				results.append(empty);
				return;
			}

			const list = document.createElement('div');
			list.className = 'ai-result-list';
			data.books.forEach((book) => {
				const item = document.createElement('article');
				item.className = 'ai-result-item';
				const title = document.createElement('a');
				title.className = 'ai-result-title';
				title.href = `/buku/${encodeURIComponent(book.id)}`;
				title.textContent = book.title;
				const details = document.createElement('span');
				details.textContent = `${book.author} · ${book.category || 'Tanpa kategori'} · Stok ${book.stock}`;
				const reason = document.createElement('p');
				reason.textContent = book.reason || '';
				item.append(title, details, reason);
				list.append(item);
			});
			results.append(list);
		} catch (error) {
			results.replaceChildren();
			const message = document.createElement('p');
			message.className = 'ai-result-status ai-result-error';
			message.textContent = error.message;
			results.append(message);
		} finally {
			button.disabled = false;
		}
	});
});

document.querySelectorAll('[data-ai-metadata]').forEach((tool) => {
	const trigger = tool.querySelector('[data-ai-metadata-trigger]');
	const results = tool.querySelector('[data-ai-metadata-results]');
	const applyButton = tool.querySelector('[data-ai-metadata-apply]');
	const token = tool.closest('form')?.querySelector('input[name="_token"]')?.value;
	let suggestions = {};

	trigger?.addEventListener('click', async () => {
		trigger.disabled = true;
		trigger.textContent = 'Membaca PDF…';
		results.hidden = false;
		results.replaceChildren();
		results.textContent = 'AI sedang meninjau metadata buku.';
		applyButton.hidden = true;

		try {
			const data = await postJson(tool.dataset.url, {
				model: tool.querySelector('[data-ai-model]')?.value,
			}, token);
			suggestions = data.suggestions || {};
			results.replaceChildren();
			const labels = {
				title: 'Judul', author: 'Penulis', publisher: 'Penerbit', isbn: 'ISBN',
				publication_year: 'Tahun terbit', category_name: 'Kategori', synopsis: 'Sinopsis',
			};
			Object.entries(labels).forEach(([field, label]) => {
				if (!suggestions[field]) return;
				const row = document.createElement('p');
				const name = document.createElement('strong');
				name.textContent = `${label}: `;
				row.append(name, document.createTextNode(String(suggestions[field])));
				results.append(row);
			});
			if (!results.childElementCount) {
				results.textContent = 'Tidak ada saran metadata yang dapat digunakan dari PDF ini.';
			} else {
				applyButton.hidden = false;
			}
		} catch (error) {
			results.textContent = error.message;
		} finally {
			trigger.disabled = false;
			trigger.innerHTML = '<i data-lucide="sparkles"></i> Buat Saran';
			createIcons({ icons: { Sparkles } });
		}
	});

	applyButton?.addEventListener('click', () => {
		Object.entries(suggestions).forEach(([field, value]) => {
			if (['title', 'author', 'publisher', 'isbn', 'publication_year', 'synopsis', 'category_id'].includes(field)) {
				const input = tool.closest('form')?.elements.namedItem(field);
				if (input && value !== null) input.value = value;
			}
		});
		applyButton.hidden = true;
		results.textContent = 'Saran diterapkan ke form. Periksa kembali sebelum menyimpan.';
	});
});

createIcons({
	icons: {
		ArchiveRestore,
		ArrowLeft,
		ArrowRight,
		ArrowUpRight,
		BookOpen,
		BookOpenCheck,
		BookPlus,
		BookX,
		CalendarDays,
		Check,
		ChevronDown,
		ChevronLeft,
		ChevronRight,
		CircleAlert,
		CircleCheck,
		Eye,
		ImagePlus,
		Layers3,
		Library,
		LibraryBig,
		ListFilter,
		LayoutDashboard,
		Menu,
		PackageX,
		Pencil,
		Plus,
		RotateCcw,
		Search,
		Send,
		SlidersHorizontal,
		Sparkles,
		Sun,
		Trash2,
		TriangleAlert,
		X,
	},
});

@php
    $event = $event ?? null;
@endphp
<script>
    function eventForm() {
        return {
            posterPreview: {{ isset($event) && $event->poster_url ? "'" . $event->poster_url . "'" : 'null' }},
            quill: null,
            init() {
                this.quill = new Quill('#editor-container', {
                    theme: 'snow',
                    placeholder: 'Syarat pendaftaran, aturan pertandingan, dsb...',
                    modules: {
                        toolbar: [
                            [{ header: [2, 3, false] }],
                            ['bold', 'italic', 'underline'],
                            [{ align: [] }],
                            [{ list: 'ordered' }, { list: 'bullet' }],
                            ['blockquote'],
                            ['link', 'image'],
                            ['clean'],
                        ],
                    },
                });
                @if (isset($event) && $event->content)
                    this.quill.root.innerHTML = @json($event->content);
                @endif
            },
            beforeSubmit() {
                this.$refs.contentInput.value = this.quill.root.innerHTML;
            },
        };
    }
</script>

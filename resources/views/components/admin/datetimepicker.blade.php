@push('styles')
    <link href="{{ asset('backend/css/daterangepicker.css') }}" rel="stylesheet" />
@endpush
@push('scripts')
    <script src="{{ asset('backend/js/moment.min.js') }}"></script>
    <script src="{{ asset('backend/js/daterangepicker.min.js') }}"></script>
    <script>
        $(document).ready(function () {
            const configMap = {
                'date': {
                    singleDatePicker: true,
                    startDate: moment(),
                    locale: { format: 'YYYY-MM-DD' }
                },
                'time': {
                    singleDatePicker: true,
                    timePicker: true,
                    timePicker24Hour: true,
                    startDate: moment(),
                    locale: { format: 'HH:mm' }
                },
                'datetime': {
                    singleDatePicker: true,
                    timePicker: true,
                    timePicker24Hour: true,
                    //startDate: moment(),   // current date & time
                    locale: { format: 'YYYY-MM-DD HH:mm' }
                },
                'daterange': {
                    locale: { format: 'YYYY-MM-DD' }
                }
            };

            $.each(configMap, function (className, config) {
                $('.' + className).daterangepicker(config);
            });
        });

    </script>
@endpush
@push('footer-scripts')

<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script>
  var pusher = new Pusher('f121ba7d19d3efa9090d', {
    cluster: 'eu'
  });

  var channel = pusher.subscribe('my-channel');
  channel.bind('my-event', function(data) {
    if (data.message == "refresh") {
        window.location.reload();
    }
  });
</script>
@endpush
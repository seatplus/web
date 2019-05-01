<div id="app">
  @if (count($errors->all()) > 0)
    <alert-component type="danger" :dismissible="true">
      <h4 slot="header">
        <i class="fas fa-ban"></i> Error
      </h4>
      <div slot="body">
        @if(is_array($message))
          @foreach ($message as $m)
            {{ $m }}
          @endforeach
        @else
          {{ $message }}
        @endif
      </div>
    </alert-component>
  @endif

  @if ($message = session('success'))
    <alert-component type="success">
      <h4 slot="header">
        <i class="fas fa-check-circle"></i> Success
      </h4>
      <div slot="body">
        @if(is_array($message))
          @foreach ($message as $m)
            {{ $m }}
          @endforeach
        @else
          {{ $message }}
        @endif
      </div>
    </alert-component>
  @endif

  @if ($message = session('error'))
    <alert-component type="danger" :dismissible="true">
      <h4 slot="header">
        <i class="fas fa-ban"></i> Error
      </h4>
      <div slot="body">
        @if(is_array($message))
          @foreach ($message as $m)
            {{ $m }}
          @endforeach
        @else
          {{ $message }}
        @endif
      </div>
    </alert-component>
  @endif

  @if ($message = session('warning'))
    <alert-component type="warning">
      <h4 slot="header">
        <i class="fas fa-exclamation-circle"></i> Warning
      </h4>
      <div slot="body">
        @if(is_array($message))
          @foreach ($message as $m)
            {{ $m }}
          @endforeach
        @else
          {{ $message }}
        @endif
      </div>
    </alert-component>
  @endif

  @if ($message = session('info'))
    <alert-component type="info">
      <h4 slot="header">
        <i class="fas fa-info-circle"></i> Info
      </h4>
      <div slot="body">
        @if(is_array($message))
          @foreach ($message as $m)
            {{ $m }}
          @endforeach
        @else
          {{ $message }}
        @endif
      </div>
    </alert-component>
  @endif
</div>


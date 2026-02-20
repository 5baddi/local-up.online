@section('script')
  $(document).ready(function() {
    $(document).scrollTop(0);

    var categoryEl = document.getElementById('category');
    if (categoryEl) {
        window.Choices && (new Choices(categoryEl, {
            classNames: {
                containerInner: categoryEl.className,
                input: 'form-control',
                inputCloned: 'form-control-sm',
                listDropdown: 'dropdown-menu',
                itemChoice: 'dropdown-item',
                activeState: 'show',
                selectedState: 'active',
            },
            shouldSort: true,
            searchEnabled: true,
        }));
    }
    
    var sortByEl = document.getElementById('sort-by');
    if (sortByEl) {
        window.Choices && (new Choices(sortByEl, {
            classNames: {
                containerInner: sortByEl.className,
                input: 'form-control',
                inputCloned: 'form-control-sm',
                listDropdown: 'dropdown-menu',
                itemChoice: 'dropdown-item',
                activeState: 'show',
                selectedState: 'active',
            },
            shouldSort: false,
            searchEnabled: false,
        }));
    }
    
    var filterByEl = document.getElementById('filter-by');
    if (filterByEl) {
        window.Choices && (new Choices(filterByEl, {
            classNames: {
                containerInner: filterByEl.className,
                input: 'form-control',
                inputCloned: 'form-control-sm',
                listDropdown: 'dropdown-menu',
                itemChoice: 'dropdown-item',
                activeState: 'show',
                selectedState: 'active',
            },
            shouldSort: false,
            searchEnabled: false,
        }));
    }

    $("#term").keyup(function(event) {
      if (event.keyCode === 13) {
          $("#search-form").submit();
      }
    });

    var debounce = function (func, wait, immediate) {
        var timeout;
        return function() {
            var context = this, args = arguments;
            var later = function() {
                    timeout = null;
                    if (!immediate) func.apply(context, args);
            };
            var callNow = immediate && !timeout;
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
            if (callNow) func.apply(context, args);
        };
    };

    var page = 1;
    var lastPage = parseInt('{{ $tweets->lastPage() }}');

    $(window).on( 'scroll', function () {
      var position = $(this).scrollTop();
      var bottom = $(document).height() - $(this).height();
  
      if(position == bottom && page < lastPage) {
        $('.custom-loader').css('display', 'block');
      }
    });

    @if($tweets->total() > 0)
    $(window).on( 'scroll', debounce(function () {
        var position = $(this).scrollTop();
        var bottom = $(document).height() - $(this).height();
  
        if(position == bottom && page < lastPage) {
          ++page;
  
          $.ajax({
              url: `{{ route('dashboard.paginate.tweets') }}?page=${page}&{{ http_build_query(Request()->query()) }}`,
              type: 'get',
              success: function(response){
                $('.custom-loader').css('display', 'none');
    
                $(response).insertBefore('.custom-loader');
              },
              error: function (req, status, error) {
                $('.custom-loader').css('display', 'none');
              }
          });
        } else {
          $('.custom-loader').css('display', 'none');
        }
      }, 1000, false));
    @endif
  });
@endsection
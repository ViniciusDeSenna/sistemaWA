<x-app-layout>
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
          <div class="col-lg-12 mb-4 order-0">
            <div class="card">
              <div class="d-flex align-items-end row">
                <div class="col-sm-7">
                    <div class="card-body">
                      <h5 class="card-title text-primary">
                        Seja bem-vindo {{ Auth::user()?->name ?? 'Desconhecido' }}! 🎉
                      </h5>
                    </div>
                  </div>
                  
                    <div class="col-sm-5 text-center text-sm-left">
                    <div class="card-body pb-0 px-0 px-md-4">
                        <img
                        src="{{asset('thema/assets/img/illustrations/man-with-laptop-light.png')}}"
                        height="140"
                        alt="View Badge User"
                        data-app-dark-img="illustrations/man-with-laptop-dark.png"
                        data-app-light-img="illustrations/man-with-laptop-light.png"
                        />
                    </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Icon container -->
        <div class="d-flex flex-wrap" id="icons-container">
            <!-- Users -->
            <div class="card icon-card cursor-pointer text-center mb-4 mx-2">
              <a class="card-body" href="{{ route('users.index') }}">
                <i class="bx bx-collection mb-2"></i>
                <p class="icon-name text-capitalize text-truncate mb-0">Usuários</p>
              </a>
            </div>
            <div class="card icon-card cursor-pointer text-center mb-4 mx-2">
                <a class="card-body" href="javascript:void(0)">
                    <i class="bx bx-collection mb-2"></i>
                    <p class="icon-name text-capitalize text-truncate mb-0">Estabelecimentos</p>
                </a>
            </div>
            <div class="card icon-card cursor-pointer text-center mb-4 mx-2">
                <a class="card-body" href="javascript:void(0)">
                    <i class="bx bx-collection mb-2"></i>
                    <p class="icon-name text-capitalize text-truncate mb-0">Colaboradores</p>
                </a>
            </div>
            <div class="card icon-card cursor-pointer text-center mb-4 mx-2">
                <a class="card-body" href="javascript:void(0)">
                    <i class="bx bx-collection mb-2"></i>
                    <p class="icon-name text-capitalize text-truncate mb-0">Diarias</p>
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
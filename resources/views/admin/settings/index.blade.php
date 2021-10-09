@extends('layouts.admin')
@section('content')
<div class="container">
    <h4><a href="{{route('admin.home')}}">🔙</a>
        @if(session('msg'))
        <p class="alert alert-success">{{ session('msg') }}</p>
        @endif
        @if(session('error'))
        <p class="alert alert-danger">{{ session('error') }}</p>
        @endif
    Settings</h4>
    <div class="row">
        <a class="col-md-3" href="{{route('settings.passcode')}}">        
            <div class="card bg-primary text-white">
                <div class="card-header">
                    <h2 class="card-title">Passcode</h2>
                </div>
                <div class="card-body">
                  
                </div>
             
            </div>
        </a>

        <a class="col-md-3" href="{{route('settings.shop')}}">        
            <div class="card bg-success text-white">
                <div class="card-header">
                    <h2 class="card-title">ဆိုင်အချက်အလက်</h2>
                </div>
                <div class="card-body">
                  
                </div>
             
            </div>
        </a>
    </div>
    <hr>
    <div class="row">
        <div class="col-md-3">
                        
            <div class="btn-group">
              <button onclick="console.log(this.children[0].style.display='inline-block')" id="upgradeBtn" type="button" class="btn btn-info dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">

                 <span id="upgradeBtnSpinner" style="display: none" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>

                🛠 Upgrade
              </button>
              <ul class="dropdown-menu">
                <li><a id="softwareUpgradeBtn" class="dropdown-item" href="#">💻 Software</a></li>
                <li><a id="databaseUpgradeBtn" class="dropdown-item" href="#">💾 Database</a></li>
                <li><a id="composerUpgradeBtn" class="dropdown-item" href="#">📀 Dependencies</a></li>
                
              </ul>
            </div>


            <button class="btn btn-secondary" id="backupBtn">
                <span id="backupBtnSpinner" style="display: none" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                Backup Data
            </button>
            {{-- <a href="/storage/backup/backup-2021-05-08_08:23:56.gz">Download</a> --}}
            <a class="btn btn-dark" href="{{ route('settings.download-backup-file') }}">Download</a>
        </div>

    </div>
</div>
@endsection
@section('js')
<script>
    const backupBtn = document.querySelector('#backupBtn');
    const upgradeBtn = document.querySelector('#upgradeBtn');
    const backupBtnSpinner = document.querySelector('#backupBtnSpinner');
    const upgradeBtnSpinner = document.querySelector('#upgradeBtnSpinner');

    const softwareUpgradeBtn = document.querySelector('#softwareUpgradeBtn');
    const databaseUpgradeBtn = document.querySelector('#databaseUpgradeBtn');
    const composerUpgradeBtn = document.querySelector('#composerUpgradeBtn');

    backupBtn.addEventListener('click', function () {
        backupBtnSpinner.style.display = 'inline-block';
        fetch (`/backup`)
        .then (res => res.json())
        .then (res => {
            if (res.isOk) {
                Toastify({
                    text: res.message,
                    backgroundColor: "linear-gradient(to right, green, lightgreen)",
                    className: "info",
                }).showToast();
                backupBtnSpinner.style.display = 'none';                    
            }
            else {                    
                Toastify({
                text: res.message,
                backgroundColor: "linear-gradient(to right, red, brown)",
                className: "info",
                }).showToast();
            }
        })
    })

    softwareUpgradeBtn.addEventListener('click', function () {
        upgradeBtnSpinner.style.display = 'inline-block';
        fetch (`/software-upgrade`)
        .then (res => res.json())
        .then (res => {
            if (res.isOk) {
                Toastify({
                    text: res.message,
                    backgroundColor: "linear-gradient(to right, green, lightgreen)",
                    className: "info",
                }).showToast();
                upgradeBtnSpinner.style.display = 'none';
            }
            else {                    
                Toastify({
                text: res.message,
                backgroundColor: "linear-gradient(to right, red, brown)",
                className: "info",
                }).showToast();
                console.log(res);
            }
        })
    })

    databaseUpgradeBtn.addEventListener('click', function () {
        upgradeBtnSpinner.style.display = 'inline-block';
        fetch (`/database-upgrade`)
        .then (res => res.json())
        .then (res => {
            if (res.isOk) {
                Toastify({
                    text: res.message,
                    backgroundColor: "linear-gradient(to right, green, lightgreen)",
                    className: "info",
                }).showToast();
                upgradeBtnSpinner.style.display = 'none';
            }
            else {                    
                Toastify({
                text: res.message,
                backgroundColor: "linear-gradient(to right, red, brown)",
                className: "info",
                }).showToast();
                console.log(res);
            }
        })
    })

    composerUpgradeBtn.addEventListener('click', function () {
        upgradeBtnSpinner.style.display = 'inline-block';
        fetch (`/composer-upgrade`)
        .then (res => res.json())
        .then (res => {
            if (res.isOk) {
                Toastify({
                    text: res.message,
                    backgroundColor: "linear-gradient(to right, green, lightgreen)",
                    className: "info",
                }).showToast();
                upgradeBtnSpinner.style.display = 'none';
            }
            else {                    
                Toastify({
                text: res.message,
                backgroundColor: "linear-gradient(to right, red, brown)",
                className: "info",
                }).showToast();
                console.log(res);
            }
        })
    })
</script>
@endsection
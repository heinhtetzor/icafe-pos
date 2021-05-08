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
    </div>
    <hr>
    <div class="row">
        <div class="col-md-3">
            {{-- <a href="/upgrade" class="btn btn-dark" onclick="console.log(this.children[0].style.display='inline-block')">
                <span>Upgrade</span>
            </a> --}}
            <button class="btn btn-danger" id="upgradeBtn">
                <span id="upgradeBtnSpinner" style="display: none" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                Upgrade
            </button>
            <button class="btn btn-secondary" id="backupBtn">
                <span id="backupBtnSpinner" style="display: none" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                Backup Data
            </button>
            {{-- <a href="/storage/backup/backup-2021-05-08_08:23:56.gz">Download</a> --}}
            <a href="{{ route('settings.download-backup-file') }}">Download</a>
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

        upgradeBtn.addEventListener('click', function () {
            upgradeBtnSpinner.style.display = 'inline-block';
            fetch (`/upgrade`)
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
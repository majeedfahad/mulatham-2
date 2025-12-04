@extends('layouts.app')


@section('content')
    <div class="container">

        @push('background')
            <div class="circle2"></div>
            <div class="circle1"></div>
            <img src="imgs/logo-desert.svg" class="myBg">
        @endpush
        @if (\App\Models\Setting::isCompetetionStart())
            <div class="col-12 p-0 rounded rounded-lg-0 my-2" id="myTable">
                @include('new-question')
                <div id="table_header" class="row text-center d-flex align-content-center" dir="rtl">
                    <div class="col-6">الفارس</div>
                    <div class="col-2">النقاط</div>
                    @if (Auth::user()->isAdmin())
                        <div class="col-4">اقصاء</div>
                    @endif
                </div>

                <!-- لوب هنا يا وحش -->
                @foreach ($activeUsers as $user)
                    <div id="table_row" class="row text-center d-flex align-content-center" dir="rtl">
                        <div class="col-6">{{$user->fakename}}</div>
                        <div class="col-2">{{$user->score}}</div>
                        @if (Auth::user()->isAdmin())
                            <div class="col-4">
                                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal"
                                        data-target="#exampleModal" data-whatever="{{$user->fakename}}"
                                        style="background-color: #b5def5; color: black; border: none;">اقصاء</button>
                            </div>
                        @endif
                        {{-- data-whatever -> Fake Name selected --}}
                    </div>
                @endforeach
                @foreach ($eliminatedUsers as $user)
                    <div id="table_row" class="row text-center d-flex align-content-center text-white" dir="rtl" style="background-color: #900808">
                        <div class="col-6">{{$user->fakename}}</div>
                        <div class="col-2">{{$user->getTotalScore()}}</div>
                        <div class="col-4">{{$user->name}}</div>
                    </div>
                @endforeach

                <!-- Modal "ما عليك منها لا تلمسها الا اذا تبي تعدل الفورم" -->
                <form action="{{route('settings.elimination')}}" method="POST">
                    @csrf
                    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
                         aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body" dir="rtl">
                                    <div class="form-group text-right">
                                        <label for="message-text" class="col-form-label">المدعي:</label>
                                        <select name="attacker" class="form-control">
                                            @foreach ($users->shuffle() as $user)
                                                <option value="{{$user->id}}">{{$user->name}}</option>
                                            @endforeach
                                        </select>
                                        {{-- loop for all users --}}
                                    </div>
                                    <div class="form-group text-right" dir="rtl">
                                        <label for="recipient-name" class="col-form-label">المتهم:</label>
                                        <input type="text" class="form-control" id="selectedId" name="fakename" readonly>
                                    </div>
                                    <div class="form-group text-right">
                                        <label for="message-text" class="col-form-label">اسمه:</label>
                                        <select name="target" class="form-control">
                                            @foreach ($users->shuffle() as $user)
                                                <option value="{{$user->name}}">{{$user->name}}</option>
                                            @endforeach
                                        </select>
                                        {{-- loop for all users --}}
                                    </div>
                                </div>
                                <div class="modal-footer" dir="rtl">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">الغاء</button>
                                    <button type="submit" class="btn btn-primary">اتهام</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            @push('scripts')
                <script>
                    $(document).ready(function() {
                        $('#exampleModal').on('show.bs.modal', function(event) {
                            var button = $(event.relatedTarget)
                            var recipient = button.data('whatever')
                            var modal = $(this)
                            modal.find('.modal-title').text('New message to ' + recipient)
                            modal.find('.modal-body input').val(recipient)
                            console.log($("input[name=fakename]").val());
                        })
                    })
                </script>
            @endpush
        @elseif(\App\Models\Setting::isCompetetionEnd())
            <div class="col-12 offset-1 align-self-center p-2 rounded rounded-lg-0 mt-3 ml-0">
                <h2 class="d-flex d-sm-flex justify-content-center text-info font-weight-light mb-2"><img
                        class="d-flex justify-content-sm-center" src="{{asset('imgs/logo-desert.svg')}}" style="height: 200px;"></h2>
                <h2 class="text-center text-white">شكرًا لتفاعلكم!</h2>
                <h2 class="text-center text-white">ونحيي الفائزين:</h2>
                @foreach ($winners as $winner)
                    <div id="table_row" class="row text-center d-flex align-content-center" dir="rtl">
                        <div class="col-6">{{$winner->name}}</div>
                        <div class="col-4">{{$winner->fakename}}</div>
                        <div class="col-2">{{$winner->getTotalScore()}}</div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="col-12 offset-1 align-self-center p-2 rounded rounded-lg-0 mt-3 ml-0">
                <h2 class="d-flex d-sm-flex justify-content-center text-info font-weight-light mb-2"><img
                        class="d-flex justify-content-sm-center" src="{{asset('imgs/logo-desert.svg')}}" style="height: 200px;"></h2>
                <h2 class="text-center text-dark font-weight-bold">يالله حيه! ننتظر طويل العمر يبدأ المسابقة</h2>
                <p class="text-center text-dark font-weight-bold">بالمناسبة ترتيب المتسابقين اللي بيطلع لك عشوائي، خد بالك وماتفضحش نفسك ;)</p>
            </div>
        @endif
    </div>
@endsection

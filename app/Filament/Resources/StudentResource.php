<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentResource\Pages;
use App\Models\Student;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?string $navigationGroup = 'Akademik';
    protected static ?string $modelLabel = 'Öğrenci';
    protected static ?string $pluralModelLabel = 'Öğrenciler';
    protected static ?string $navigationLabel = 'Öğrenciler';
    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Step::make('Öğrenci Kaynağı')
                        ->icon('heroicon-o-user-plus')
                        ->columns(2)
                        ->schema([
                            Radio::make('enrollment_mode')
                                ->label('Kayıt Kaynağı')
                                ->options([
                                    'new' => 'Yeni Öğrenci',
                                    'existing' => 'Mevcut Öğrenci',
                                ])
                                ->inline()
                                ->live()
                                ->default('new'),

                            Select::make('existing_student_id')
                                ->label('Mevcut Öğrenci')
                                ->options(fn () => Student::query()
                                    ->orderBy('name')
                                    ->get()
                                    ->mapWithKeys(fn (Student $student) => [$student->id => $student->full_name])
                                    ->toArray())
                                ->searchable()
                                ->preload()
                                ->live()
                                ->helperText('Önceki yıllarda kayıt edilen öğrenciyi seçtiğinizde bilgiler otomatik doldurulur.')
                                ->hidden(fn (Get $get) => $get('enrollment_mode') !== 'existing')
                                ->required(fn (Get $get) => $get('enrollment_mode') === 'existing')
                                ->afterStateUpdated(function ($state, Set $set) {
                                    if (! $state) {
                                        return;
                                    }

                                    $student = Student::query()->find($state);

                                    if (! $student) {
                                        return;
                                    }

                                    $attributes = Arr::only($student->getAttributes(), [
                                        'first_name',
                                        'last_name',
                                        'birth_date',
                                        'gender',
                                        'photo_path',
                                        'registration_type',
                                        'identity_type',
                                        'identity_number',
                                        'email',
                                        'phone',
                                        'status',
                                        'campus_id',
                                        'uid',
                                        'school_name',
                                        'education_year',
                                        'school_number',
                                        'class_level',
                                        'section',
                                        'registered_by',
                                        'entry_date',
                                        'entry_type',
                                        'education_type',
                                        'foreign_language',
                                        'previous_school',
                                        'reference',
                                        'heard_from',
                                        'takes_meal_service',
                                        'education_preference',
                                        'uses_transport',
                                        'second_language',
                                        'address_line1',
                                        'address_line2',
                                        'country',
                                        'province',
                                        'district',
                                        'subdistrict',
                                        'neighborhood',
                                        'postal_code',
                                        'latitude',
                                        'longitude',
                                        'guardian_relationship',
                                        'guardian_national_id',
                                        'guardian_first_name',
                                        'guardian_last_name',
                                        'guardian_email',
                                        'guardian_phone',
                                        'guardian_receive_sms',
                                        'guardian_education_level',
                                        'guardian_profession_group',
                                        'guardian_work_type',
                                        'guardian_profession',
                                        'guardian_job_title',
                                        'guardian_employer',
                                        'note',
                                    ]);

                                    foreach ($attributes as $key => $value) {
                                        $set($key, $value);
                                    }
                                }),
                        ]),

                    Step::make('Öğrenci Bilgileri')
                        ->icon('heroicon-o-identification')
                        ->columns(2)
                        ->schema([
                            FileUpload::make('photo_path')
                                ->label('Öğrenci Fotoğrafı')
                                ->image()
                                ->directory('students/photos')
                                ->imageEditor()
                                ->imagePreviewHeight('200')
                                ->columnSpanFull(),

                            Select::make('registration_type')
                                ->label('Kayıt Türü')
                                ->options(self::registrationTypeOptions())
                                ->required(fn (Get $get) => $get('enrollment_mode') === 'new'),

                            Select::make('identity_type')
                                ->label('Kimlik Türü')
                                ->options([
                                    'tc' => 'T.C. Kimlik Numarası',
                                    'passport' => 'Pasaport Numarası',
                                    'foreign' => 'Yabancı Kimlik Numarası',
                                ])
                                ->required(fn (Get $get) => $get('enrollment_mode') === 'new'),

                            TextInput::make('identity_number')
                                ->label('Kimlik Numarası')
                                ->maxLength(50)
                                ->required(fn (Get $get) => $get('enrollment_mode') === 'new'),

                            TextInput::make('first_name')
                                ->label('Adı')
                                ->maxLength(100)
                                ->required(fn (Get $get) => $get('enrollment_mode') === 'new'),

                            TextInput::make('last_name')
                                ->label('Soyadı')
                                ->maxLength(100)
                                ->required(fn (Get $get) => $get('enrollment_mode') === 'new'),

                            DatePicker::make('birth_date')
                                ->label('Doğum Tarihi')
                                ->native(false)
                                ->displayFormat('Y-m-d')
                                ->closeOnDateSelection()
                                ->required(fn (Get $get) => $get('enrollment_mode') === 'new'),

                            Select::make('gender')
                                ->label('Cinsiyet')
                                ->options([
                                    'female' => 'Kadın',
                                    'male' => 'Erkek',
                                    'other' => 'Diğer',
                                ])
                                ->searchable()
                                ->nullable(),

                            TextInput::make('email')
                                ->label('E-Posta')
                                ->email()
                                ->maxLength(150)
                                ->nullable(),

                            TextInput::make('phone')
                                ->label('Cep Telefonu')
                                ->tel()
                                ->maxLength(30)
                                ->nullable(),

                            Select::make('status')
                                ->label('Öğrenci Durumu')
                                ->options([
                                    'active' => 'Aktif',
                                    'passive' => 'Pasif',
                                    'graduated' => 'Mezun',
                                ])
                                ->default('active')
                                ->required(),
                        ]),

                    Step::make('Kayıt Bilgileri')
                        ->icon('heroicon-o-academic-cap')
                        ->columns(2)
                        ->schema([
                            TextInput::make('campus_id')
                                ->label('Kampüs ID')
                                ->numeric()
                                ->nullable()
                                ->dehydrateStateUsing(fn ($state) => $state === '' ? null : $state),

                            Select::make('school_name')
                                ->label('Okul')
                                ->options([
                                    'anaokulu' => 'Anaokulu',
                                    'ilkokul' => 'İlkokul',
                                    'ortaokul' => 'Ortaokul',
                                    'lise' => 'Lise',
                                ])
                                ->searchable()
                                ->nullable(),

                            TextInput::make('education_year')
                                ->label('Eğitim Yılı')
                                ->default(fn () => sprintf('%s-%s', now()->year, now()->addYear()->year))
                                ->maxLength(20)
                                ->required(),

                            TextInput::make('uid')
                                ->label('Öğrenci No / UID')
                                ->maxLength(50)
                                ->unique(ignoreRecord: true)
                                ->nullable(),

                            TextInput::make('school_number')
                                ->label('Okul Numarası')
                                ->helperText('Boş bırakılırsa kayıttan önce otomatik oluşturulur.')
                                ->maxLength(50)
                                ->nullable(),

                            Select::make('class_level')
                                ->label('Sınıf Seviyesi')
                                ->options(self::classLevelOptions())
                                ->searchable()
                                ->nullable(),

                            TextInput::make('section')
                                ->label('Şube')
                                ->maxLength(30)
                                ->nullable(),

                            TextInput::make('registered_by')
                                ->label('Kaydı Alan Kişi')
                                ->default(fn () => Auth::user()?->name)
                                ->disabled()
                                ->dehydrated(true)
                                ->maxLength(150)
                                ->nullable(),

                            DatePicker::make('entry_date')
                                ->label('Giriş Tarihi')
                                ->native(false)
                                ->default(fn () => now())
                                ->displayFormat('Y-m-d')
                                ->required(),

                            Select::make('entry_type')
                                ->label('Giriş Türü')
                                ->options([
                                    'ilk_kayit' => 'İlk Kayıt',
                                    'nakil' => 'Nakil',
                                    'yeniden' => 'Yeniden Kayıt',
                                ])
                                ->searchable()
                                ->nullable(),

                            Select::make('education_type')
                                ->label('Eğitim Şekli')
                                ->options([
                                    'tam_zamanli' => 'Tam Zamanlı',
                                    'yari_zamanli' => 'Yarı Zamanlı',
                                    'uzaktan' => 'Uzaktan Eğitim',
                                ])
                                ->searchable()
                                ->nullable(),

                            Select::make('education_preference')
                                ->label('Eğitim Şekli Tercihi')
                                ->options([
                                    'gunduz' => 'Gündüz',
                                    'yatili' => 'Yatılı',
                                    'hibrit' => 'Hibrit',
                                ])
                                ->searchable()
                                ->nullable(),

                            Select::make('foreign_language')
                                ->label('Yabancı Dil')
                                ->options(self::foreignLanguageOptions())
                                ->searchable()
                                ->nullable(),

                            Select::make('second_language')
                                ->label('2. Yabancı Dil')
                                ->options(self::secondLanguageOptions())
                                ->default('none')
                                ->searchable()
                                ->nullable(),

                            Toggle::make('takes_meal_service')
                                ->label('Yemek Hizmeti Alıyor mu?')
                                ->default(false),

                            Toggle::make('uses_transport')
                                ->label('Servis Kullanıyor mu?')
                                ->default(false),

                            TextInput::make('previous_school')
                                ->label('Geldiği Okul')
                                ->maxLength(150)
                                ->nullable(),

                            TextInput::make('reference')
                                ->label('Referans')
                                ->maxLength(150)
                                ->nullable(),

                            TextInput::make('heard_from')
                                ->label('Okulumuzu Nereden Duydunuz?')
                                ->maxLength(150)
                                ->nullable(),

                            Textarea::make('note')
                                ->label('Not')
                                ->rows(3)
                                ->columnSpanFull()
                                ->maxLength(1000)
                                ->nullable(),
                        ]),

                    Step::make('Adres Bilgileri')
                        ->icon('heroicon-o-map')
                        ->columns(2)
                        ->schema([
                            TextInput::make('address_line1')
                                ->label('Adres Satırı 1')
                                ->maxLength(255)
                                ->columnSpanFull()
                                ->nullable(),

                            TextInput::make('address_line2')
                                ->label('Adres Satırı 2')
                                ->maxLength(255)
                                ->columnSpanFull()
                                ->nullable(),

                            TextInput::make('country')
                                ->label('Ülke')
                                ->maxLength(100)
                                ->nullable(),

                            TextInput::make('province')
                                ->label('İl')
                                ->maxLength(100)
                                ->nullable(),

                            TextInput::make('district')
                                ->label('İlçe')
                                ->maxLength(100)
                                ->nullable(),

                            TextInput::make('subdistrict')
                                ->label('Semt')
                                ->maxLength(100)
                                ->nullable(),

                            TextInput::make('neighborhood')
                                ->label('Mahalle')
                                ->maxLength(100)
                                ->nullable(),

                            TextInput::make('postal_code')
                                ->label('Posta Kodu')
                                ->maxLength(20)
                                ->nullable(),

                            TextInput::make('latitude')
                                ->label('Enlem')
                                ->numeric()
                                ->nullable(),

                            TextInput::make('longitude')
                                ->label('Boylam')
                                ->numeric()
                                ->nullable(),
                        ]),

                    Step::make('Veli Bilgileri')
                        ->icon('heroicon-o-user-group')
                        ->columns(2)
                        ->schema([
                            Radio::make('guardian_mode')
                                ->label('Veli Kaynağı')
                                ->options([
                                    'new' => 'Yeni Kişi',
                                    'existing' => 'Mevcut Kişiden Kopyala',
                                ])
                                ->inline()
                                ->default('new')
                                ->live(),

                            Select::make('guardian_copy_student_id')
                                ->label('Kopyalanacak Öğrenci')
                                ->options(fn () => Student::query()
                                    ->whereNotNull('guardian_first_name')
                                    ->orderBy('name')
                                    ->get()
                                    ->mapWithKeys(fn (Student $student) => [$student->id => $student->full_name])
                                    ->toArray())
                                ->searchable()
                                ->preload()
                                ->hidden(fn (Get $get) => $get('guardian_mode') !== 'existing')
                                ->required(fn (Get $get) => $get('guardian_mode') === 'existing')
                                ->helperText('Seçilen öğrencinin veli bilgileri bu kayda kopyalanır.')
                                ->afterStateUpdated(function ($state, Set $set) {
                                    if (! $state) {
                                        return;
                                    }

                                    $student = Student::query()->find($state);

                                    if (! $student) {
                                        return;
                                    }

                                    $attributes = Arr::only($student->getAttributes(), [
                                        'guardian_relationship',
                                        'guardian_national_id',
                                        'guardian_first_name',
                                        'guardian_last_name',
                                        'guardian_email',
                                        'guardian_phone',
                                        'guardian_receive_sms',
                                        'guardian_education_level',
                                        'guardian_profession_group',
                                        'guardian_work_type',
                                        'guardian_profession',
                                        'guardian_job_title',
                                        'guardian_employer',
                                    ]);

                                    foreach ($attributes as $key => $value) {
                                        $set($key, $value);
                                    }
                                }),

                            Select::make('guardian_relationship')
                                ->label('Yakınlık Türü')
                                ->options([
                                    'mother' => 'Anne',
                                    'father' => 'Baba',
                                    'guardian' => 'Vasi',
                                    'relative' => 'Akraba',
                                    'other' => 'Diğer',
                                ])
                                ->required(),

                            TextInput::make('guardian_national_id')
                                ->label('Veli Kimlik No')
                                ->maxLength(50)
                                ->required(fn (Get $get) => $get('guardian_mode') === 'new'),

                            TextInput::make('guardian_first_name')
                                ->label('Veli Adı')
                                ->maxLength(100)
                                ->required(fn (Get $get) => $get('guardian_mode') === 'new'),

                            TextInput::make('guardian_last_name')
                                ->label('Veli Soyadı')
                                ->maxLength(100)
                                ->required(fn (Get $get) => $get('guardian_mode') === 'new'),

                            TextInput::make('guardian_email')
                                ->label('Veli E-Posta')
                                ->email()
                                ->maxLength(150)
                                ->nullable(),

                            TextInput::make('guardian_phone')
                                ->label('Veli Telefon No')
                                ->tel()
                                ->maxLength(30)
                                ->nullable(),

                            Toggle::make('guardian_receive_sms')
                                ->label('Veli SMS Alsın mı?')
                                ->default(false),

                            Select::make('guardian_education_level')
                                ->label('Eğitim Seviyesi')
                                ->options(self::guardianEducationLevelOptions())
                                ->nullable()
                                ->searchable(),

                            TextInput::make('guardian_profession_group')
                                ->label('Meslek Grubu')
                                ->maxLength(150)
                                ->nullable(),

                            Select::make('guardian_work_type')
                                ->label('Çalışma Şekli')
                                ->options(self::guardianWorkTypeOptions())
                                ->nullable()
                                ->searchable(),

                            TextInput::make('guardian_profession')
                                ->label('Meslek')
                                ->maxLength(150)
                                ->nullable(),

                            TextInput::make('guardian_job_title')
                                ->label('Görev Ünvanı')
                                ->maxLength(150)
                                ->nullable(),

                            TextInput::make('guardian_employer')
                                ->label('Çalıştığı Kurum')
                                ->maxLength(150)
                                ->nullable(),
                        ]),
                ])
                    ->columnSpanFull()
                    ->skippable()
                    ->persistStepInQueryString()
                    ->nextActionLabel('İleri')
                    ->previousActionLabel('Geri')
                    ->submitActionLabel('Kaydet'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('photo_path')
                    ->label('Fotoğraf')
                    ->circular()
                    ->defaultImageUrl(fn (Student $record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->full_name ?: 'Öğrenci'))
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('school_number')
                    ->label('Okul No')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('uid')
                    ->label('UID')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('full_name')
                    ->label('Ad Soyad')
                    ->searchable(['first_name', 'last_name', 'name'])
                    ->sortable()
                    ->wrap(),

                Tables\Columns\TextColumn::make('education_year')
                    ->label('Eğitim Yılı')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('class_level')
                    ->label('Sınıf')
                    ->formatStateUsing(fn (?string $state) => self::classLevelOptions()[$state] ?? $state)
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('section')
                    ->label('Şube')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('registration_type')
                    ->label('Kayıt Türü')
                    ->badge()
                    ->formatStateUsing(fn (?string $state) => self::registrationTypeOptions()[$state] ?? $state)
                    ->color(fn (?string $state) => match ($state) {
                        'asil' => 'success',
                        'misafir' => 'warning',
                        'yaz_okulu' => 'info',
                        'kis_okulu' => 'info',
                        'degisim' => 'secondary',
                        default => 'gray',
                    })
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('campus_id')
                    ->label('Kampüs')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('status')
                    ->label('Durum')
                    ->badge()
                    ->color(fn (?string $state) => match ($state) {
                        'active' => 'success',
                        'graduated' => 'info',
                        'passive' => 'gray',
                        default => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('guardian_full_name')
                    ->label('Veli')
                    ->state(fn (Student $record) => trim(collect([$record->guardian_first_name, $record->guardian_last_name])->filter()->join(' ')))
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->wrap(),

                Tables\Columns\IconColumn::make('takes_meal_service')
                    ->label('Yemek')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\IconColumn::make('uses_transport')
                    ->label('Servis')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Kayıt')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Güncelleme')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Durum')
                    ->options([
                        'active' => 'Aktif',
                        'passive' => 'Pasif',
                        'graduated' => 'Mezun',
                    ]),

                Tables\Filters\SelectFilter::make('registration_type')
                    ->label('Kayıt Türü')
                    ->options(self::registrationTypeOptions()),

                Tables\Filters\SelectFilter::make('education_year')
                    ->label('Eğitim Yılı')
                    ->options(fn () => Student::query()
                        ->whereNotNull('education_year')
                        ->orderByDesc('education_year')
                        ->pluck('education_year', 'education_year')
                        ->all()),

                Tables\Filters\TernaryFilter::make('takes_meal_service')
                    ->label('Yemek Hizmeti'),

                Tables\Filters\TernaryFilter::make('uses_transport')
                    ->label('Servis Kullanımı'),

                Tables\Filters\Filter::make('created_today')
                    ->label('Bugün oluşturulanlar')
                    ->query(fn ($q) => $q->whereDate('created_at', now()->toDateString())),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    protected static function registrationTypeOptions(): array
    {
        return [
            'asil' => 'Asil',
            'misafir' => 'Misafir',
            'yaz_okulu' => 'Yaz Okulu',
            'kis_okulu' => 'Kış Okulu',
            'degisim' => 'Değişim',
        ];
    }

    protected static function classLevelOptions(): array
    {
        return collect(range(1, 12))
            ->mapWithKeys(fn (int $level) => [(string) $level => $level . '. Sınıf'])
            ->prepend('Hazırlık', 'hazirlik')
            ->toArray();
    }

    protected static function foreignLanguageOptions(): array
    {
        return [
            'english' => 'İngilizce',
            'german' => 'Almanca',
            'french' => 'Fransızca',
            'arabic' => 'Arapça',
            'spanish' => 'İspanyolca',
            'other' => 'Diğer',
        ];
    }

    protected static function secondLanguageOptions(): array
    {
        return [
            'none' => 'Yok',
            'english' => 'İngilizce',
            'german' => 'Almanca',
            'french' => 'Fransızca',
            'spanish' => 'İspanyolca',
            'arabic' => 'Arapça',
            'russian' => 'Rusça',
        ];
    }

    protected static function guardianEducationLevelOptions(): array
    {
        return [
            'primary' => 'İlkokul',
            'secondary' => 'Ortaokul',
            'high' => 'Lise',
            'associate' => 'Ön Lisans',
            'bachelor' => 'Lisans',
            'master' => 'Yüksek Lisans',
            'phd' => 'Doktora',
        ];
    }

    protected static function guardianWorkTypeOptions(): array
    {
        return [
            'full_time' => 'Tam Zamanlı',
            'part_time' => 'Yarı Zamanlı',
            'self_employed' => 'Serbest',
            'freelance' => 'Freelance',
            'unemployed' => 'Çalışmıyor',
        ];
    }

    public static function generateSchoolNumber(?string $educationYear): string
    {
        $yearKey = $educationYear ?? now()->format('Y');
        $normalizedYear = preg_replace('/[^0-9]/', '', (string) $yearKey) ?: now()->format('Y');

        $lastSequence = Student::query()
            ->when($educationYear, fn ($query) => $query->where('education_year', $educationYear))
            ->whereNotNull('school_number')
            ->orderByDesc('school_number')
            ->value('school_number');

        $sequence = 1;

        if ($lastSequence && preg_match('/(\d+)$/', $lastSequence, $matches)) {
            $sequence = ((int) $matches[1]) + 1;
        }

        return sprintf('%s-%04d', $normalizedYear, $sequence);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListStudents::route('/'),
            'create' => Pages\CreateStudent::route('/create'),
            'edit'   => Pages\EditStudent::route('/{record}/edit'),
        ];
    }
}

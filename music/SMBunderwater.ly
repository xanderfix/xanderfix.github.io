#(define (tie::tab-clear-tied-fret-numbers grob)
   (let* ((tied-fret-nr (ly:spanner-bound grob RIGHT)))
      (ly:grob-set-property! tied-fret-nr 'transparent #t)))

\version "2.10.5"
\paper {
   indent = #30
   printallheaders = ##t
   print-all-headers = ##t
   ragged-right = ##f
   ragged-bottom = ##t
}
\layout {
   \context { \Score
      \override MetronomeMark #'padding = #'5
   }
   \context { \Staff
      \override TimeSignature #'style = #'numbered
      \override StringNumber #'transparent = ##t
   }
   \context { \TabStaff
      \override TimeSignature #'style = #'numbered
      \override Stem #'transparent = ##t
      \override Beam #'transparent = ##t
      \override Tie  #'after-line-breaking = #tie::tab-clear-tied-fret-numbers
   }
   \context { \TabVoice
      \override Tie #'stencil = ##f
   }
   \context { \StaffGroup
      \consists "Instrument_name_engraver"
   }
}
deadNote = #(define-music-function (parser location note) (ly:music?)
   (set! (ly:music-property note 'tweaks)
      (acons 'stencil ly:note-head::print
         (acons 'glyph-name "2cross"
            (acons 'style 'special
               (ly:music-property note 'tweaks)))))
   note)

palmMute = #(define-music-function (parser location note) (ly:music?)
   (set! (ly:music-property note 'tweaks)
      (acons 'style 'do (ly:music-property note 'tweaks)))
   note)

TrackAVoiceAMusic = #(define-music-function (parser location inTab) (boolean?)
#{
   \tempo 4=220
   \clef #(if $inTab "tab" "treble_8")
   \key c \major
   \time 3/4
   \oneVoice
   <d'\3>4 <e'\3>4 <fis'\3>4 
   <g'\2>4 <a'\2>4 <ais'\2>4 
   <b'\2>8 <b'\2>8 \times 2/3 {<b'\2>4 r8 <b'\2>4 r8 } 
   <b'\2>2 r8 <g'\3>8 
   <e''\1>2. 
   <dis''\1>2. 
   <e''\1>2. 
   r8 <g'\2>8 <a'\2>8 <b'\2>8 <c''\2>8 <d''\2>8 
   <e''\1>2. 
   <dis''\1>2 <f''\1>4 
   <e''\1>2. 
   r2 r8 <g'\3>8 
   <d''\1>2. 
   <cis''\1>2. 
   <d''\1>2. 
   r8 <g'\2>8 <a'\2>8 <b'\2>8 <c''\2>8 <cis''\2>8 
   <d''\1>2. 
   <g'\2>2 <f''\1>4 
   <e''\1>2. 
   r2 r8 <g'\3>8 
   <g''\1>2. 
   <g''\1>2. 
   <g''\1>2. 
   <g''\1>4 <a''\1>4 r8 <g''\1>8 
   <f''\1>2. 
   <f''\1>2. 
   <f''\1>2. 
   <f''\1>4 <g''\1>4 r8 <f''\1>8 
   <e''\1>2. 
   <a'\2>4 <b'\2>4 <f''\1>4 
   <e''\1>8 <e''\1>8 <e''\1>4. <b'\2>8 
   <c''\2>2. 
   \bar "|."
   \pageBreak
#})
TrackAVoiceBMusic = #(define-music-function (parser location inTab) (boolean?)
#{
#})
TrackALyrics = \lyricmode {
   \set ignoreMelismata = ##t
   
   \unset ignoreMelismata
}
TrackAStaff = \new Staff <<
   \context Voice = "TrackAVoiceAMusic" {
      \TrackAVoiceAMusic ##f
   }
   \context Voice = "TrackAVoiceBMusic" {
      \TrackAVoiceBMusic ##f
   }
>>
TrackATabStaff = \new TabStaff \with { stringTunings = #'( 4 -1 -5 -10 -15 -20 ) } <<
   \context TabVoice = "TrackAVoiceAMusic" {
      \removeWithTag #'chords
      \removeWithTag #'texts
      \TrackAVoiceAMusic ##t
   }
   \context TabVoice = "TrackAVoiceBMusic" {
      \removeWithTag #'chords
      \removeWithTag #'texts
      \TrackAVoiceBMusic ##t
   }
>>
TrackAStaffGroup = \new StaffGroup <<
   \set StaffGroup.instrumentName = #""
   \TrackAStaff
   \TrackATabStaff
>>
TrackBVoiceAMusic = #(define-music-function (parser location inTab) (boolean?)
#{
   \tempo 4=220
   \clef #(if $inTab "tab" "treble_8")
   \key c \major
   \time 3/4
   \oneVoice
   <d'\3>4 <cis'\3>4 <c'\3>4 
   <b\3>4 <c'\3>4 <cis'\3>4 
   <d'\3>8 <d'\3>8 <d'\3>4 <e'\3>4 
   <f'\3>2 r4 
   <g'\3>2. 
   <fis'\3>2. 
   <g'\3>2. 
   r2. 
   <g'\2>2. 
   <fis'\2>2 <a'\2>4 
   <g'\2>2. 
   r2. 
   <f'\3>2. 
   <e'\3>2. 
   <f'\3>2. 
   r2. 
   <f'\3>2. 
   <b\4>2 <a'\2>4 
   <g'\2>2. 
   r2. 
   <e''\2>2. 
   <d''\2>2. 
   <cis''\2>2. 
   r2. 
   <d''\2>2. 
   <cis''\2>2. 
   <c''\2>2. 
   r2. 
   <c'\4>2. 
   <f'\3>4 <g'\3>4 <b'\2>4 
   <b'\2>8 <b'\2>8 <b'\2>4 r8 <f'\3>8 
   <e'\3>2. 
   \bar "|."
   \pageBreak
#})
TrackBVoiceBMusic = #(define-music-function (parser location inTab) (boolean?)
#{
#})
TrackBLyrics = \lyricmode {
   \set ignoreMelismata = ##t
   
   \unset ignoreMelismata
}
TrackBStaff = \new Staff <<
   \context Voice = "TrackBVoiceAMusic" {
      \TrackBVoiceAMusic ##f
   }
   \context Voice = "TrackBVoiceBMusic" {
      \TrackBVoiceBMusic ##f
   }
>>
TrackBTabStaff = \new TabStaff \with { stringTunings = #'( 4 -1 -5 -10 -15 -20 ) } <<
   \context TabVoice = "TrackBVoiceAMusic" {
      \removeWithTag #'chords
      \removeWithTag #'texts
      \TrackBVoiceAMusic ##t
   }
   \context TabVoice = "TrackBVoiceBMusic" {
      \removeWithTag #'chords
      \removeWithTag #'texts
      \TrackBVoiceBMusic ##t
   }
>>
TrackBStaffGroup = \new StaffGroup <<
   \set StaffGroup.instrumentName = #""
   \TrackBStaff
   \TrackBTabStaff
>>
TrackCVoiceAMusic = #(define-music-function (parser location inTab) (boolean?)
#{
   \tempo 4=220
   \clef #(if $inTab "tab" "treble_8")
   \key c \major
   \time 3/4
   \oneVoice
   r2. 
   r2. 
   r2 <g\3>4 
   <g\3>2. 
   <c\5>4 <g\3>4 <c'\2>4 
   <b,\5>4 <g\3>4 <b\2>4 
   <c\5>4 <g\3>4 <c'\2>4 
   <e\4>4 <g\3>4 <c'\2>4 
   <c\5>4 <g\3>4 <c'\2>4 
   <b,\5>4 <g\3>4 <b\2>4 
   <c\5>4 <g\3>4 <c'\2>4 
   <e\4>4 <g\3>4 <c'\2>4 
   <d\4>4 <g\3>4 <b\2>4 
   <cis\5>4 <fis\4>4 <ais\3>4 
   <d\4>4 <g\3>4 <b\2>4 
   <b,\5>4 <g\3>4 <b\2>4 
   <d\4>4 <g\3>4 <b\2>4 
   <b,\5>4 <g\3>4 <b\2>4 
   <c\5>4 <g\3>4 <c'\2>4 
   <g,\6>4 <g\3>4 <c'\2>4 
   <c\5>4 <g\3>4 <e'\1>4 
   <b,\5>4 <g\3>4 <d'\2>4 
   <ais,\5>4 <g\3>4 <cis'\2>4 
   <cis\5>4 <g\3>4 <e'\1>4 
   <d\4>4 <a\3>4 <f'\1>4 
   <cis\5>4 <a\3>4 <f'\1>4 
   <c\5>4 <a\3>4 <f'\1>4 
   <b,\5>4 <g\3>4 <f'\1>4 
   <c,\7>4 <g\3>4 <e'\1>4 
   <g,\6>4 <g\3>4 <g\3>4 
   <f\4>8 <f\4>8 <f\4>4 r8 <b,\5>8 
   <c\5>2. 
   \bar "|."
   \pageBreak
#})
TrackCVoiceBMusic = #(define-music-function (parser location inTab) (boolean?)
#{
#})
TrackCLyrics = \lyricmode {
   \set ignoreMelismata = ##t
   
   \unset ignoreMelismata
}
TrackCStaff = \new Staff <<
   \context Voice = "TrackCVoiceAMusic" {
      \TrackCVoiceAMusic ##f
   }
   \context Voice = "TrackCVoiceBMusic" {
      \TrackCVoiceBMusic ##f
   }
>>
TrackCTabStaff = \new TabStaff \with { stringTunings = #'( 4 -1 -5 -10 -15 -20 -25 ) } <<
   \context TabVoice = "TrackCVoiceAMusic" {
      \removeWithTag #'chords
      \removeWithTag #'texts
      \TrackCVoiceAMusic ##t
   }
   \context TabVoice = "TrackCVoiceBMusic" {
      \removeWithTag #'chords
      \removeWithTag #'texts
      \TrackCVoiceBMusic ##t
   }
>>
TrackCStaffGroup = \new StaffGroup <<
   \set StaffGroup.instrumentName = #""
   \TrackCStaff
   \TrackCTabStaff
>>
TrackDVoiceAMusic = #(define-music-function (parser location inTab) (boolean?)
#{
   \tempo 4=220
   \clef #(if $inTab "tab" "treble_8")
   \key c \major
   \time 3/4
   \oneVoice
   r4 <fis,\1>8 r8 <fis,\1>4 
   r4 <fis,\1>8 <fis,\1>8 <fis,\1>4 
   r4 <fis,\1>8 r8 <fis,\1>4 
   r4 <fis,\1>8 <fis,\1>8 <fis,\1>4 
   r4 <fis,\1>8 r8 <fis,\1>4 
   r4 <fis,\1>8 <fis,\1>8 <fis,\1>4 
   r4 <fis,\1>8 r8 <fis,\1>4 
   r4 <fis,\1>8 <fis,\1>8 <fis,\1>4 
   r4 <fis,\1>8 r8 <fis,\1>4 
   r4 <fis,\1>8 <fis,\1>8 <fis,\1>4 
   r4 <fis,\1>8 r8 <fis,\1>4 
   r4 <fis,\1>8 <fis,\1>8 <fis,\1>4 
   r4 <fis,\1>8 r8 <fis,\1>4 
   r4 <fis,\1>8 <fis,\1>8 <fis,\1>4 
   r4 <fis,\1>8 r8 <fis,\1>4 
   r4 <fis,\1>8 <fis,\1>8 <fis,\1>4 
   r4 <fis,\1>8 r8 <fis,\1>4 
   r4 <fis,\1>8 <fis,\1>8 <fis,\1>4 
   r4 <fis,\1>8 r8 <fis,\1>4 
   r4 <fis,\1>8 <fis,\1>8 <fis,\1>4 
   r4 <fis,\1>8 r8 <fis,\1>4 
   r4 <fis,\1>8 <fis,\1>8 <fis,\1>4 
   r4 <fis,\1>8 r8 <fis,\1>4 
   r4 <fis,\1>8 <fis,\1>8 <fis,\1>4 
   r4 <fis,\1>8 r8 <fis,\1>4 
   r4 <fis,\1>8 <fis,\1>8 <fis,\1>4 
   r4 <fis,\1>8 r8 <fis,\1>4 
   r4 <fis,\1>8 <fis,\1>8 <fis,\1>4 
   r4 <fis,\1>8 r8 <fis,\1>4 
   r4 <fis,\1>8 <fis,\1>8 <fis,\1>4 
   r4 <fis,\1>8 r8 <fis,\1>4 
   r4 <fis,\1>8 <fis,\1>8 <fis,\1>4 
   \bar "|."
   \pageBreak
#})
TrackDVoiceBMusic = #(define-music-function (parser location inTab) (boolean?)
#{
#})
TrackDLyrics = \lyricmode {
   \set ignoreMelismata = ##t
   
   \unset ignoreMelismata
}
TrackDStaff = \new Staff <<
   \context Voice = "TrackDVoiceAMusic" {
      \TrackDVoiceAMusic ##f
   }
   \context Voice = "TrackDVoiceBMusic" {
      \TrackDVoiceBMusic ##f
   }
>>
TrackDTabStaff = \new TabStaff \with { stringTunings = #'( -60 -60 -60 -60 -60 -60 ) } <<
   \context TabVoice = "TrackDVoiceAMusic" {
      \removeWithTag #'chords
      \removeWithTag #'texts
      \TrackDVoiceAMusic ##t
   }
   \context TabVoice = "TrackDVoiceBMusic" {
      \removeWithTag #'chords
      \removeWithTag #'texts
      \TrackDVoiceBMusic ##t
   }
>>
TrackDStaffGroup = \new StaffGroup <<
   \set StaffGroup.instrumentName = #""
   \TrackDStaff
   \TrackDTabStaff
>>
\score {
   <<
   \TrackAStaffGroup
   \TrackBStaffGroup
   \TrackCStaffGroup
   \TrackDStaffGroup
   >>
   \header {
      title = "" 
      composer = "" 
   }
}
